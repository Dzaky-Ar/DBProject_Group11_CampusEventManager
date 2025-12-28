<?php
session_start();
require_once "OrganizerQueries.php";

if (!isset($_SESSION['User_name'])) {
    header("Location: ../../Main_menu/Login_Page.php");
    exit();
}

$organizer = new Organizer();
$organizer_id = $organizer->getOrganizerID($_SESSION['Email']);

// Fetch organizer history
$history = $organizer->getOrganizerHistory($organizer_id);
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../../../styling/style_organizer.css">
        <meta name="viewport" content="width-device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link rel="icon" href="../../../images/logo web.png">
        <title>Dashboard</title>
        <style>
            #notification {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                z-index: 1000 !important;
            }
        </style>
    </head>

    <body>

        <div id="notification" style="display: none; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; background-color: #f0f0f0; padding: 10px; text-align: center; z-index: 1000;"></div>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() { const notification = document.getElementById('notification'); notification.className = 'success-message'; notification.textContent = '" . addslashes($_SESSION['success_message']) . "'; notification.style.display = 'block'; setTimeout(() => { notification.style.display = 'none'; }, 5000); });</script>";
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() { const notification = document.getElementById('notification'); notification.className = 'error-message'; notification.textContent = '" . addslashes($_SESSION['error_message']) . "'; notification.style.display = 'block'; setTimeout(() => { notification.style.display = 'none'; }, 5000); });</script>";
            unset($_SESSION['error_message']);
        }
        ?>

        <nav id="sidebar">
            <ul>
                <li>
                    <span>Dashboard</span>
                </li>
                <li>
                    <a href="Organizer_Dashboard.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z"/></svg>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="Entries.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M600-80v-80h160v-400H200v160h-80v-320q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H600ZM320 0l-56-56 103-104H40v-80h327L264-344l56-56 200 200L320 0ZM200-640h560v-80H200v80Zm0 0v-80 80Z"/></svg>
                        <span>Entries</span>
                    </a>
                </li>
                <li>
                    <a href="Inventories.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M600-80v-80h160v-400H200v160h-80v-320q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H600ZM320 0l-56-56 103-104H40v-80h327L264-344l56-56 200 200L320 0ZM200-640h560v-80H200v80Zm0 0v-80 80Z"/></svg>
                        <span>Inventories</span>
                    </a>
                </li>
                <li>
                    <a href="RecordEntries.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-120q-138 0-240.5-91.5T122-440h82q14 104 92.5 172T480-200q117 0 198.5-81.5T760-480q0-117-81.5-198.5T480-760q-69 0-129 32t-101 88h110v80H120v-240h80v94q51-64 124.5-99T480-840q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-120Zm112-192L440-464v-216h80v184l128 128-56 56Z"/></svg>
                        <span>History</span>
                    </a>
                </li>
                <li>
                    <a href="Settings.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/></svg>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a href="../../../index.html">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>


        <main>
            <main>
            <div class="container">
                <h2 style="text-align:center; margin: 20px;">Submission History</h2>

                <?php if ($history && count($history) > 0): ?>
                    <table class="history-table">
                        <tr>
                            <th>Location</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Event Name</th>
                            <th>Description</th>
                            <th>Proposal</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Update</th>
                            <th>Cancel</th>
                        </tr>

                        <?php foreach ($history as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Lokasi']) ?></td>
                            <td><?= htmlspecialchars($row['Waktu']) ?></td>
                            <td><p class="status-<?= strtolower(str_replace(' ', '-', $row['Status'])) ?>"><?= htmlspecialchars($row['Status']) ?></p></td>
                            <td><?= htmlspecialchars($row['Nama_event']) ?></td>
                            <td><?= htmlspecialchars($row['Description']) ?></td>
                            <td>
                                <?php if ($row['Proposal'] === 'Yes'): ?>
                                    <a href="DownloadProposal.php?proposal_id=<?= $row['Proposal_ID'] ?>" target="_blank">View Proposal</a>
                                <?php else: ?>
                                    No File
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td><?= htmlspecialchars($row['jumlah']) ?></td>
                            <td>
                                <button id="btn2" onclick="location.href='updateEntries.php?proposal_id=<?= $row['Proposal_ID'] ?>'">Update</button>
                            </td>
                            <td>
                                <button id="btn2" onclick="cancelSubmission(<?= $row['Proposal_ID'] ?>)">Cancel</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>

                <?php else: ?>
                    <p style="text-align:center;">No submissions found.</p>
                <?php endif; ?>

            </div>
</main>


        </main>


        

    <script>
        function cancelSubmission(proposalId) {
            if (confirm('Are you sure you want to cancel this submission?')) {
                fetch('CancelSubmission.php?proposal_id=' + proposalId + '&ajax=1')
                    .then(response => response.json())
                    .then(data => {
                        const notification = document.getElementById('notification');
                        notification.className = data.status + '-message';
                        notification.textContent = data.message;
                        notification.style.display = 'block';
                        setTimeout(() => {
                            notification.style.display = 'none';
                        }, 5000); // Hide after 5 seconds
                        // Optionally refresh the page or update the table
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const notification = document.getElementById('notification');
                        notification.className = 'error-message';
                        notification.textContent = 'An error occurred.';
                        notification.style.display = 'block';
                        setTimeout(() => {
                            notification.style.display = 'none';
                        }, 5000);
                    });
            }
        }
    </script>
    </body>
</html>
