<?php
require_once '../includes/auth.php';
requireMember();
require_once '../config/db.php';
$pageTitle = 'My Timetable';

$stmt = $conn->prepare("SELECT member_id FROM members WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$member = $stmt->fetch();

// Handle cancel
if (isset($_GET['cancel'])) {
    $cancelId = (int)$_GET['cancel'];
    $conn->prepare("UPDATE session_bookings SET booking_status = 'Cancelled' WHERE booking_id = ? AND member_id = ? AND booking_status IN ('Pending','Approved')")->execute([$cancelId, $member['member_id']]);
    header("Location: timetable.php?msg=Session cancelled successfully");
    exit();
}

// Get current week offset
$weekOffset = isset($_GET['week']) ? (int)$_GET['week'] : 0;

// Calculate the start of the week (Monday)
$today = new DateTime();
$today->modify("{$weekOffset} week");
$dayOfWeek = (int)$today->format('N'); // 1=Mon, 7=Sun
$today->modify('-' . ($dayOfWeek - 1) . ' days');
$weekStart = clone $today;
$weekEnd = clone $today;
$weekEnd->modify('+6 days');

$startStr = $weekStart->format('Y-m-d');
$endStr = $weekEnd->format('Y-m-d');

// Fetch sessions for this week
$weekSessions = $conn->prepare("SELECT sb.*, t.trainer_name, t.session_fee, t.specialization 
    FROM session_bookings sb 
    JOIN trainers t ON sb.trainer_id = t.trainer_id 
    WHERE sb.member_id = ? 
    AND sb.session_date BETWEEN ? AND ? 
    AND sb.booking_status IN ('Pending','Approved') 
    ORDER BY sb.session_date ASC, sb.session_time ASC");
$weekSessions->execute([$member['member_id'], $startStr, $endStr]);
$sessions = $weekSessions->fetchAll();

// Build lookup: date => time => session
$sessionMap = [];
foreach ($sessions as $s) {
    $sessionMap[$s['session_date']][$s['session_time']] = $s;
}

// Get all unique trainer names for search autocomplete
$allTrainers = $conn->prepare("SELECT DISTINCT t.trainer_name FROM session_bookings sb JOIN trainers t ON sb.trainer_id = t.trainer_id WHERE sb.member_id = ? AND sb.booking_status IN ('Pending','Approved')");
$allTrainers->execute([$member['member_id']]);
$trainerNames = $allTrainers->fetchAll(PDO::FETCH_COLUMN);

// Time slots
$timeSlots = ['8:00 AM','9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM','6:00 PM','7:00 PM','8:00 PM'];

// Days array
$days = [];
for ($i = 0; $i < 7; $i++) {
    $d = clone $weekStart;
    $d->modify("+{$i} days");
    $days[] = $d;
}

// Past / history
$history = $conn->prepare("SELECT sb.*, t.trainer_name, t.session_fee FROM session_bookings sb JOIN trainers t ON sb.trainer_id = t.trainer_id WHERE sb.member_id = ? AND (sb.session_date < CURDATE() OR sb.booking_status IN ('Completed','Cancelled','Rejected')) ORDER BY sb.session_date DESC LIMIT 20");
$history->execute([$member['member_id']]);
$pastSessions = $history->fetchAll();

require_once '../includes/header.php';
?>

<style>
/* ===== Calendar Timetable Styles ===== */
.timetable-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.timetable-controls .week-nav {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.timetable-controls .week-nav .week-label {
    font-family: var(--font-heading);
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-primary);
    min-width: 220px;
    text-align: center;
}
.timetable-controls .week-nav .btn-week {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--bg-input);
    border: 1px solid var(--border);
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
    font-size: 0.9rem;
}
.timetable-controls .week-nav .btn-week:hover {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
    transform: scale(1.1);
}

/* Search */
.trainer-search-box {
    position: relative;
    min-width: 280px;
}
.trainer-search-box input {
    width: 100%;
    padding: 0.65rem 1rem 0.65rem 2.5rem;
    background: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: 50px;
    color: var(--text-primary);
    font-size: 0.9rem;
    font-family: var(--font-body);
    outline: none;
    transition: var(--transition);
}
.trainer-search-box input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(67,97,238,0.15);
}
.trainer-search-box input::placeholder {
    color: var(--text-muted);
}
.trainer-search-box .search-icon {
    position: absolute;
    left: 0.9rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.85rem;
    pointer-events: none;
    transition: var(--transition);
}
.trainer-search-box input:focus ~ .search-icon {
    color: var(--accent);
}
.search-results-count {
    display: none;
    margin-top: 0.5rem;
    padding: 0.4rem 0.9rem;
    background: rgba(67,97,238,0.1);
    border: 1px solid rgba(67,97,238,0.2);
    border-radius: 50px;
    font-size: 0.8rem;
    color: var(--accent);
    font-weight: 500;
}
.search-results-count.visible {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    animation: slideDown 0.3s ease;
}

/* Calendar Grid */
.calendar-wrapper {
    overflow-x: auto;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    background: var(--bg-card);
    backdrop-filter: blur(10px);
    margin-bottom: 1.5rem;
}
.calendar-table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
    table-layout: fixed;
}
.calendar-table thead th {
    padding: 1rem 0.5rem;
    text-align: center;
    font-family: var(--font-heading);
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-secondary);
    background: rgba(67,97,238,0.08);
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    z-index: 2;
}
.calendar-table thead th:first-child {
    width: 90px;
    background: rgba(67,97,238,0.12);
}
.calendar-table thead th .day-name {
    display: block;
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-bottom: 0.15rem;
}
.calendar-table thead th .day-date {
    display: block;
    font-size: 1rem;
    color: var(--text-primary);
    font-weight: 700;
}
.calendar-table thead th.today-col {
    background: rgba(67,97,238,0.18);
}
.calendar-table thead th.today-col .day-date {
    color: var(--accent);
}

.calendar-table tbody td {
    padding: 0.35rem;
    border: 1px solid var(--border);
    vertical-align: top;
    height: 72px;
    transition: background 0.2s ease;
}
.calendar-table tbody td:first-child {
    text-align: center;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-muted);
    background: rgba(67,97,238,0.04);
    vertical-align: middle;
    padding: 0.5rem 0.3rem;
}
.calendar-table tbody td.today-col {
    background: rgba(67,97,238,0.04);
}

/* Session Card in Calendar */
.session-cell {
    background: linear-gradient(135deg, rgba(67,97,238,0.15), rgba(114,9,183,0.12));
    border: 1px solid rgba(67,97,238,0.25);
    border-radius: 8px;
    padding: 0.4rem 0.5rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0.15rem;
    cursor: default;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.session-cell::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: var(--gradient);
    border-radius: 3px 0 0 3px;
}
.session-cell.status-pending {
    background: linear-gradient(135deg, rgba(255,209,102,0.12), rgba(255,170,50,0.08));
    border-color: rgba(255,209,102,0.3);
}
.session-cell.status-pending::before {
    background: linear-gradient(180deg, #ffd166, #ffaa32);
}
.session-cell .session-trainer {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.session-cell .session-type {
    font-size: 0.65rem;
    color: var(--text-secondary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.session-cell .session-status {
    font-size: 0.6rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.session-cell .session-status.approved { color: var(--success); }
.session-cell .session-status.pending { color: var(--warning); }
.session-cell .session-actions {
    position: absolute;
    top: 2px;
    right: 4px;
}
.session-cell .cancel-btn {
    background: none;
    border: none;
    color: var(--danger);
    cursor: pointer;
    font-size: 0.7rem;
    opacity: 0.5;
    transition: var(--transition);
    padding: 2px;
}
.session-cell .cancel-btn:hover {
    opacity: 1;
    transform: scale(1.2);
}

/* Highlighted sessions (search match) */
.session-cell.highlighted {
    border-color: var(--accent) !important;
    box-shadow: 0 0 12px rgba(67,97,238,0.4), 0 0 24px rgba(67,97,238,0.15);
    animation: pulseGlow 2s ease-in-out infinite;
    z-index: 1;
    transform: scale(1.03);
}
.session-cell.highlighted::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 8px;
    background: rgba(67,97,238,0.08);
    pointer-events: none;
}

@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 0 12px rgba(67,97,238,0.4), 0 0 24px rgba(67,97,238,0.15); }
    50% { box-shadow: 0 0 20px rgba(67,97,238,0.6), 0 0 40px rgba(67,97,238,0.25); }
}

/* Today button */
.btn-today {
    padding: 0.4rem 1rem;
    border-radius: 50px;
    background: var(--gradient);
    color: #fff;
    font-size: 0.8rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: var(--transition);
    font-family: var(--font-body);
}
.btn-today:hover {
    box-shadow: 0 4px 15px rgba(67,97,238,0.4);
    transform: translateY(-1px);
}

/* Legend */
.calendar-legend {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
    padding: 0.7rem 1rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.8rem;
    color: var(--text-secondary);
}
.calendar-legend .legend-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.calendar-legend .legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 3px;
}
.calendar-legend .legend-dot.approved {
    background: linear-gradient(135deg, #4361ee, #7209b7);
}
.calendar-legend .legend-dot.pending {
    background: linear-gradient(135deg, #ffd166, #ffaa32);
}
.calendar-legend .legend-dot.highlighted {
    background: var(--accent);
    box-shadow: 0 0 6px rgba(67,97,238,0.6);
}

/* Empty state */
.calendar-empty-week {
    text-align: center;
    padding: 2rem;
    color: var(--text-muted);
    font-size: 0.9rem;
}
.calendar-empty-week i {
    font-size: 2rem;
    display: block;
    margin-bottom: 0.5rem;
    opacity: 0.4;
}

/* Responsive */
@media (max-width: 768px) {
    .timetable-controls {
        flex-direction: column;
        align-items: stretch;
    }
    .timetable-controls .week-nav {
        justify-content: center;
    }
    .trainer-search-box {
        min-width: unset;
    }
}
</style>

<div class="container fade-in">
    <div class="page-header">
        <h1><i class="fas fa-calendar-alt"></i> My Timetable</h1>
        <p>View your booked trainers in a weekly calendar view</p>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <div style="margin-bottom:1.5rem; display:flex; gap:0.75rem; flex-wrap:wrap;">
        <a href="book_session.php" class="btn btn-primary"><i class="fas fa-plus"></i> Book New Session</a>
        <a href="trainers.php" class="btn btn-secondary"><i class="fas fa-user-tie"></i> View Trainers</a>
    </div>

    <!-- Search & Week Navigation -->
    <div class="timetable-controls">
        <div class="week-nav">
            <a href="timetable.php?week=<?php echo $weekOffset - 1; ?>" class="btn-week" title="Previous Week">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php if ($weekOffset !== 0): ?>
                <a href="timetable.php?week=0" class="btn-today">Today</a>
            <?php endif; ?>
            <span class="week-label">
                <?php echo $weekStart->format('M d'); ?> — <?php echo $weekEnd->format('M d, Y'); ?>
            </span>
            <a href="timetable.php?week=<?php echo $weekOffset + 1; ?>" class="btn-week" title="Next Week">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
        <div class="trainer-search-box">
            <input type="text" id="trainerSearch" placeholder="Search booked trainer..." autocomplete="off">
            <i class="fas fa-search search-icon"></i>
        </div>
    </div>

    <div id="searchResultsCount" class="search-results-count"></div>

    <!-- Legend -->
    <div class="calendar-legend">
        <div class="legend-item"><span class="legend-dot approved"></span> Approved</div>
        <div class="legend-item"><span class="legend-dot pending"></span> Pending</div>
        <div class="legend-item"><span class="legend-dot highlighted"></span> Search Match</div>
    </div>

    <!-- Calendar Timetable -->
    <div class="calendar-wrapper">
        <table class="calendar-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <?php
                    $todayStr = (new DateTime())->format('Y-m-d');
                    $dayNames = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                    foreach ($days as $idx => $day):
                        $isToday = $day->format('Y-m-d') === $todayStr;
                    ?>
                    <th class="<?php echo $isToday ? 'today-col' : ''; ?>">
                        <span class="day-name"><?php echo $dayNames[$idx]; ?></span>
                        <span class="day-date"><?php echo $day->format('d'); ?></span>
                    </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeSlots as $time): ?>
                <tr>
                    <td><?php echo $time; ?></td>
                    <?php foreach ($days as $day):
                        $dateStr = $day->format('Y-m-d');
                        $isToday = $dateStr === $todayStr;
                        $session = $sessionMap[$dateStr][$time] ?? null;
                    ?>
                    <td class="<?php echo $isToday ? 'today-col' : ''; ?>">
                        <?php if ($session): ?>
                        <div class="session-cell <?php echo $session['booking_status'] === 'Pending' ? 'status-pending' : ''; ?>"
                             data-trainer="<?php echo htmlspecialchars(strtolower($session['trainer_name'])); ?>"
                             title="<?php echo htmlspecialchars($session['trainer_name']); ?> — <?php echo $session['session_type']; ?> (<?php echo $session['booking_status']; ?>)&#10;Fee: RM <?php echo number_format($session['session_fee'], 2); ?>">
                            <div class="session-actions">
                                <a href="timetable.php?cancel=<?php echo $session['booking_id']; ?>&week=<?php echo $weekOffset; ?>" 
                                   class="cancel-btn" 
                                   onclick="return confirm('Cancel this session with <?php echo htmlspecialchars($session['trainer_name']); ?>?');"
                                   title="Cancel Session">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                            <span class="session-trainer"><?php echo htmlspecialchars($session['trainer_name']); ?></span>
                            <span class="session-type"><?php echo $session['session_type']; ?></span>
                            <span class="session-status <?php echo strtolower($session['booking_status']); ?>">
                                <?php echo $session['booking_status']; ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (empty($sessions)): ?>
    <div class="calendar-empty-week">
        <i class="fas fa-calendar-times"></i>
        No sessions booked for this week. <a href="book_session.php" style="color:var(--accent);">Book one now!</a>
    </div>
    <?php endif; ?>

    <!-- Session History -->
    <div class="card" style="margin-top:1rem;">
        <div class="card-header">
            <h3><i class="fas fa-history" style="color:var(--text-muted);"></i> Session History</h3>
        </div>
        <?php if (empty($pastSessions)): ?>
            <p style="text-align:center;color:var(--text-muted);padding:1rem;">No past sessions.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Date</th><th>Time</th><th>Trainer</th><th>Type</th><th>Fee</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($pastSessions as $s): ?>
                        <tr>
                            <td><?php echo $s['session_date']; ?></td>
                            <td><?php echo $s['session_time']; ?></td>
                            <td><?php echo htmlspecialchars($s['trainer_name']); ?></td>
                            <td><?php echo $s['session_type']; ?></td>
                            <td>RM <?php echo number_format($s['session_fee'], 2); ?></td>
                            <td><span class="badge <?php
                                echo match($s['booking_status']) {
                                    'Completed' => 'badge-info',
                                    'Approved' => 'badge-success',
                                    'Cancelled' => 'badge-danger',
                                    'Rejected' => 'badge-danger',
                                    default => 'badge-warning'
                                }; ?>"><?php echo $s['booking_status']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Trainer search & highlight
(function() {
    const searchInput = document.getElementById('trainerSearch');
    const resultsBadge = document.getElementById('searchResultsCount');
    const sessionCells = document.querySelectorAll('.session-cell');

    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        let matchCount = 0;

        sessionCells.forEach(cell => {
            cell.classList.remove('highlighted');

            if (query.length > 0) {
                const trainerName = cell.getAttribute('data-trainer') || '';
                if (trainerName.includes(query)) {
                    cell.classList.add('highlighted');
                    matchCount++;
                }
            }
        });

        if (query.length > 0) {
            resultsBadge.innerHTML = '<i class="fas fa-search"></i> ' + matchCount + ' session' + (matchCount !== 1 ? 's' : '') + ' found for "' + this.value.trim() + '"';
            resultsBadge.classList.add('visible');

            // Scroll to first highlighted session
            const firstMatch = document.querySelector('.session-cell.highlighted');
            if (firstMatch) {
                firstMatch.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
            }
        } else {
            resultsBadge.classList.remove('visible');
        }
    });
})();
</script>

<?php require_once '../includes/footer.php'; ?>
