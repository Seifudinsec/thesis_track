<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Read Operation: Fetch submissions and check for feedback
$query = "SELECT s.*, f.comments, f.grade 
          FROM submissions s 
          LEFT JOIN feedback f ON s.id = f.submission_id 
          WHERE s.student_id = ? 
          ORDER BY s.submitted_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$student_id]);
$submissions = $stmt->fetchAll();
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>My Thesis Submissions</h2>
    <a href="upload.php" class="btn btn-primary">+ Submit New Thesis</a>
</div>

<div class="card">
    <h3>Submission History</h3>
    <?php if (count($submissions) > 0): ?>
        <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
            <thead>
                <tr style="background: var(--secondary-bg); text-align: left;">
                    <th style="padding: 1rem;">Title</th>
                    <th style="padding: 1rem;">Status</th>
                    <th style="padding: 1rem;">Grade</th>
                    <th style="padding: 1rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $sub): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 1rem;">
                            <strong><?php echo htmlspecialchars($sub['title']); ?></strong><br>
                            <small style="color: #666;">Submitted: <?php echo date('M d, Y', strtotime($sub['submitted_at'])); ?></small>
                        </td>
                        <td style="padding: 1rem;">
                            <span class="status-badge" style="padding: 0.3rem 0.6rem; border-radius: 20px; font-size: 0.8rem; background: <?php 
                                echo $sub['status'] == 'approved' ? '#d4edda' : ($sub['status'] == 'rejected' ? '#f8d7da' : '#fff3cd'); 
                            ?>; color: <?php 
                                echo $sub['status'] == 'approved' ? '#155724' : ($sub['status'] == 'rejected' ? '#721c24' : '#856404'); 
                            ?>;">
                                <?php echo ucfirst(str_replace('_', ' ', $sub['status'])); ?>
                            </span>
                        </td>
                        <td style="padding: 1rem; font-weight: bold; color: var(--main-nav);">
                            <?php echo $sub['grade'] ? htmlspecialchars($sub['grade']) : '-'; ?>
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 10px;">
                                <a href="../<?php echo $sub['file_path']; ?>" target="_blank" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">View PDF</a>
                                
                                <?php if ($sub['status'] == 'pending'): ?>
                                    <a href="edit_submission.php?id=<?php echo $sub['id']; ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background-color: #A8D0E6; color: #24305E;">Edit</a>
                                    <a href="delete_submission.php?id=<?php echo $sub['id']; ?>" class="btn btn-action" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this submission?')">Delete</a>
                                <?php endif; ?>

                                <?php if ($sub['comments']): ?>
                                    <button onclick="alert('Supervisor Feedback: <?php echo addslashes(htmlspecialchars($sub['comments'])); ?>')" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background-color: #F8E9A1; color: #24305E;">Feedback</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="margin-top: 1rem; color: #666;">You haven't submitted anything yet. Click the button above to start.</p>
    <?php endif; ?>
</div>

<section class="student-explanation" style="margin-top: 3rem; background: #eee; padding: 1.5rem; border-radius: 8px;">
    <h3>🎓 Student Explanation: Full CRUD Dashboard</h3>
    <p><strong>Logic Flow:</strong> We are now using a <code>LEFT JOIN</code> to fetch both the submission AND any feedback that exists for it in one go.</p>
    <p><strong>CRUD Operations:</strong>
        <ul>
            <li><strong>Read (R):</strong> Displaying the list of submissions.</li>
            <li><strong>Delete (D):</strong> Added a link to <code>delete_submission.php</code> (protected for 'pending' items only).</li>
            <li><strong>Update (U):</strong> Added a link to <code>edit_submission.php</code>.</li>
        </ul>
    </p>
    <p><strong>Security:</strong> We restrict "Edit" and "Delete" actions to submissions that are still <code>pending</code>. Once a supervisor starts reviewing, the student cannot change the file.</p>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
