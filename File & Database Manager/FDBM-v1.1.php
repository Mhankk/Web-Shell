<?php
session_start();

/**
 * Basic authentication class. Test edit
 */
class Auth {
    // Hard-coded credentials – change these for production!
    private $username = 'admin';
    private $password = 'password';

    public function login($user, $pass) {
        if ($user === $this->username && $pass === $this->password) {
            $_SESSION['logged_in'] = true;
            return true;
        }
        return false;
    }

    public function check() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function logout() {
        session_destroy();
    }
}

/**
 * FileManager class encapsulates file system operations.
 * This version (for the Files menu) allows browsing the entire file system.
 */
class FileManager {
    private $baseDir;

    // Set the default base directory to the root of the file system.
    public function __construct($baseDir = '/') {
        $this->baseDir = realpath($baseDir);
    }

    // If $path is absolute (starts with DIRECTORY_SEPARATOR), use it directly.
    // Otherwise, append it to the baseDir.
    public function resolvePath($path) {
        if (substr($path, 0, 1) === DIRECTORY_SEPARATOR) {
            $realPath = realpath($path);
        } else {
            $realPath = realpath($this->baseDir . DIRECTORY_SEPARATOR . $path);
        }
        return $realPath;
    }

    public function listDirectory($path = '') {
        $dir = $this->resolvePath($path);
        if (!$dir || !is_dir($dir)) {
            return false;
        }
        return scandir($dir);
    }

    public function readFile($file) {
        $filePath = $this->resolvePath($file);
        if (!$filePath || !is_file($filePath)) {
            return false;
        }
        return file_get_contents($filePath);
    }

    public function createFile($file, $content = '') {
        $filePath = $this->resolvePath($file);
        if (!$filePath) {
            $filePath = (substr($file, 0, 1) === DIRECTORY_SEPARATOR) ? $file : $this->baseDir . DIRECTORY_SEPARATOR . $file;
        }
        if (file_exists($filePath)) {
            return false;
        }
        return file_put_contents($filePath, $content) !== false;
    }

    public function updateFile($file, $content) {
        $filePath = $this->resolvePath($file);
        if (!$filePath || !is_file($filePath)) {
            return false;
        }
        return file_put_contents($filePath, $content) !== false;
    }

    public function deleteFile($file) {
        $filePath = $this->resolvePath($file);
        if (!$filePath) {
            return false;
        }
        if (is_dir($filePath)) {
            return rmdir($filePath); // Only works for empty directories.
        } else {
            return unlink($filePath);
        }
    }

    public function makeDirectory($dir) {
        $dirPath = (substr($dir, 0, 1) === DIRECTORY_SEPARATOR) ? $dir : $this->baseDir . DIRECTORY_SEPARATOR . $dir;
        if (file_exists($dirPath)) {
            return false;
        }
        return mkdir($dirPath, 0755, true);
    }

    public function renameFile($oldName, $newName) {
        $oldPath = $this->resolvePath($oldName);
        $newPath = (substr($newName, 0, 1) === DIRECTORY_SEPARATOR) ? $newName : $this->baseDir . DIRECTORY_SEPARATOR . $newName;
        if (!$oldPath || !file_exists($oldPath)) {
            return false;
        }
        return rename($oldPath, $newPath);
    }
}

/**
 * DBManager class provides minimal database management functionality.
 * It uses PDO to connect to a MySQL server and lets you list databases,
 * list tables, show table structure, and execute custom queries.
 */
class DBManager {
    private $pdo;
    public function __construct($host, $user, $pass, $dbname = null) {
        $dsn = "mysql:host=$host" . ($dbname ? ";dbname=$dbname" : "");
        try {
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }
    }
    public function listDatabases() {
        $stmt = $this->pdo->query("SHOW DATABASES");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    public function listTables() {
        $stmt = $this->pdo->query("SHOW TABLES");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    public function showTableStructure($table) {
        $stmt = $this->pdo->query("DESCRIBE `$table`");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function executeQuery($query) {
        $stmt = $this->pdo->query($query);
        if($stmt->columnCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return "Query executed successfully.";
        }
    }
}

// ------------------------------
// Authentication and Login Check
// ------------------------------
$auth = new Auth();

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Process login if not authenticated.
if (!$auth->check() && isset($_POST['username'], $_POST['password'])) {
    if ($auth->login($_POST['username'], $_POST['password'])) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = "Invalid credentials";
    }
}

// If not logged in, display login form.
if (!$auth->check()):
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Backend Tool</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h2 class="mt-5">Login</h2>
      <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
      <form method="post" class="mt-3">
        <div class="mb-3">
          <label class="form-label">Username:</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password:</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
      </form>
    </div>
  </div>
</div>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
exit;
endif;

// -----------------------
// Navigation Menu Section
// -----------------------
$menu = $_GET['menu'] ?? 'files';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Backend Tool</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Backend Tool</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarNav" aria-controls="navbarNav" 
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php echo ($menu=='files')?'active':''; ?>" href="?menu=files">Files</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($menu=='database')?'active':''; ?>" href="?menu=database">Database</a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
         <li class="nav-item">
           <a class="nav-link" href="?logout=1">Logout</a>
         </li>
      </ul>
    </div>
  </div>
</nav>

<?php
// --------------------------------------------------
// Database Management Section (Minimal Adminer-like)
// --------------------------------------------------
if ($menu == 'database') {
    // Database connection parameters (adjust as needed).
    $host = 'localhost';
    $dbUser = 'root';
    $dbPass = '';
    $dbname = $_GET['db'] ?? null; // If a database is selected, it appears here.

    try {
        $dbManager = new DBManager($host, $dbUser, $dbPass, $dbname);
    } catch (PDOException $e) {
        echo "<div class='container mt-4 alert alert-danger'>DB Connection error: " . $e->getMessage() . "</div>";
        exit;
    }

    echo "<div class='container mt-4'>";
    // If no database is selected, list all databases.
    if (!$dbname) {
        echo "<h3>Databases</h3>";
        $databases = $dbManager->listDatabases();
        echo "<table class='table table-bordered'>";
        echo "<thead><tr><th>Name</th><th>Action</th></tr></thead><tbody>";
        foreach ($databases as $db) {
            echo "<tr><td>" . htmlspecialchars($db) . "</td><td>";
            echo "<a class='btn btn-sm btn-primary' href='?menu=database&db=" . urlencode($db) . "'>Select</a>";
            echo "</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        // A database is selected.
        echo "<h3>Database: " . htmlspecialchars($dbname) . "</h3>";
        // If a specific table is selected, show its structure.
        if (isset($_GET['table'])) {
            $table = $_GET['table'];
            echo "<h4>Table Structure: " . htmlspecialchars($table) . "</h4>";
            $structure = $dbManager->showTableStructure($table);
            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr></thead><tbody>";
            foreach ($structure as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            echo "<a class='btn btn-sm btn-info' href='?menu=database&db=" . urlencode($dbname) . "'>Back to Tables</a>";
        } else {
            // List tables in the selected database.
            echo "<h4>Tables</h4>";
            $tables = $dbManager->listTables();
            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>Name</th><th>Action</th></tr></thead><tbody>";
            foreach ($tables as $table) {
                echo "<tr><td>" . htmlspecialchars($table) . "</td><td>";
                echo "<a class='btn btn-sm btn-secondary' href='?menu=database&db=" . urlencode($dbname) . "&table=" . urlencode($table) . "'>Structure</a>";
                echo "</td></tr>";
            }
            echo "</tbody></table>";
        }
        // SQL Query Execution Form.
        echo "<h4 class='mt-4'>Run SQL Query</h4>";
        echo "<form method='post'>";
        echo "<div class='mb-3'><textarea name='query' class='form-control' rows='5' placeholder='Enter SQL query here'></textarea></div>";
        echo "<button type='submit' class='btn btn-primary'>Execute</button>";
        echo "</form>";
        if (isset($_POST['query'])) {
            echo "<h5 class='mt-4'>Query Result:</h5>";
            try {
                $result = $dbManager->executeQuery($_POST['query']);
                if (is_array($result)) {
                    if (count($result) > 0) {
                        echo "<table class='table table-bordered'><thead><tr>";
                        foreach (array_keys($result[0]) as $col) {
                            echo "<th>" . htmlspecialchars($col) . "</th>";
                        }
                        echo "</tr></thead><tbody>";
                        foreach ($result as $row) {
                            echo "<tr>";
                            foreach ($row as $value) {
                                echo "<td>" . htmlspecialchars($value) . "</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<p>No results returned.</p>";
                    }
                } else {
                    echo "<p>" . htmlspecialchars($result) . "</p>";
                }
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Error executing query: " . $e->getMessage() . "</div>";
            }
        }
    }
    echo "</div>";
    exit;
}

// --------------------------------------------------
// File Manager Section (when menu == 'files')
// --------------------------------------------------
$baseDirectory = '/'; // Allow full file system browsing.
$fileManager = new FileManager($baseDirectory);
$action = $_GET['action'] ?? '';
$message = '';

if ($action === 'mkdir' && isset($_POST['dirname'])) {
    if ($fileManager->makeDirectory($_POST['dirname'])) {
        $message = "Directory created successfully.";
    } else {
        $message = "Failed to create directory or it already exists.";
    }
} elseif ($action === 'createFile' && isset($_POST['filename'])) {
    $content = $_POST['content'] ?? '';
    if ($fileManager->createFile($_POST['filename'], $content)) {
        $message = "File created successfully.";
    } else {
        $message = "Failed to create file or it already exists.";
    }
} elseif ($action === 'updateFile' && isset($_POST['filename'])) {
    $content = $_POST['content'] ?? '';
    if ($fileManager->updateFile($_POST['filename'], $content)) {
        $message = "File updated successfully.";
    } else {
        $message = "Failed to update file.";
    }
} elseif ($action === 'delete' && isset($_GET['target'])) {
    if ($fileManager->deleteFile($_GET['target'])) {
        $message = "Deleted successfully.";
    } else {
        $message = "Failed to delete.";
    }
} elseif ($action === 'rename' && isset($_POST['newName'], $_POST['oldName'])) {
    if ($fileManager->renameFile($_POST['oldName'], $_POST['newName'])) {
        $message = "Renamed successfully.";
    } else {
        $message = "Failed to rename.";
    }
}

$currentDir = $_GET['dir'] ?? '';
$files = $fileManager->listDirectory($currentDir);
?>

<div class="container my-4">
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <h4>Current Directory: <?php echo htmlspecialchars($currentDir ? $currentDir : '/'); ?></h4>
    <table class="table table-bordered table-striped mt-3">
         <thead>
             <tr>
                 <th>Name</th>
                 <th>Type</th>
                 <th>Actions</th>
             </tr>
         </thead>
         <tbody>
             <?php
             if ($files !== false) {
                 foreach ($files as $file) {
                     if ($file == '.' || $file == '..') continue;
                     $filePath = ($currentDir ? $currentDir . '/' : '') . $file;
                     $fullPath = $fileManager->resolvePath($filePath);
                     echo "<tr>";
                     echo "<td>" . htmlspecialchars($file) . "</td>";
                     echo "<td>" . (is_dir($fullPath) ? "Directory" : "File") . "</td>";
                     echo "<td>";
                     if (is_dir($fullPath)) {
                         echo "<a href='?dir=" . urlencode($filePath) . "' class='btn btn-sm btn-info'>Open</a> ";
                     } else {
                         echo "<a href='?action=edit&target=" . urlencode($filePath) . "' class='btn btn-sm btn-warning'>Edit</a> ";
                     }
                     echo "<a href='?action=rename&target=" . urlencode($filePath) . "' class='btn btn-sm btn-secondary'>Rename</a> ";
                     echo "<a href='?action=delete&target=" . urlencode($filePath) . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\");'>Delete</a>";
                     echo "</td>";
                     echo "</tr>";
                 }
             } else {
                 echo "<tr><td colspan='3'>Unable to read directory.</td></tr>";
             }
             ?>
         </tbody>
    </table>

    <div class="row mt-4">
         <div class="col-md-6">
             <h4>Create Directory</h4>
             <form method="post" action="?action=mkdir<?php echo $currentDir ? "&dir=" . urlencode($currentDir) : ""; ?>">
                 <div class="mb-3">
                     <label class="form-label">Directory Name:</label>
                     <input type="text" name="dirname" class="form-control" required>
                 </div>
                 <button type="submit" class="btn btn-primary">Create Directory</button>
             </form>
         </div>
         <div class="col-md-6">
             <h4>Create File</h4>
             <form method="post" action="?action=createFile<?php echo $currentDir ? "&dir=" . urlencode($currentDir) : ""; ?>">
                 <div class="mb-3">
                     <label class="form-label">File Name:</label>
                     <input type="text" name="filename" class="form-control" required>
                 </div>
                 <div class="mb-3">
                     <label class="form-label">Content:</label>
                     <textarea name="content" rows="5" class="form-control"></textarea>
                 </div>
                 <button type="submit" class="btn btn-primary">Create File</button>
             </form>
         </div>
    </div>

    <?php
    if ($action === 'edit' && isset($_GET['target'])) {
        $target = $_GET['target'];
        $content = $fileManager->readFile($target);
        ?>
         <div class="mt-5">
             <h4>Edit File Content: <?php echo htmlspecialchars(basename($target)); ?></h4>
             <form method="post" action="?action=updateFile&dir=<?php echo urlencode($currentDir); ?>">
                 <input type="hidden" name="filename" value="<?php echo htmlspecialchars($target); ?>">
                 <div class="mb-3">
                     <textarea name="content" rows="10" class="form-control"><?php echo htmlspecialchars($content); ?></textarea>
                 </div>
                 <button type="submit" class="btn btn-primary">Update File</button>
             </form>
         </div>
    <?php 
    } elseif ($action === 'rename' && isset($_GET['target'])) {
        $target = $_GET['target'];
        ?>
         <div class="mt-5">
             <h4>Rename: <?php echo htmlspecialchars(basename($target)); ?></h4>
             <form method="post" action="?action=rename&dir=<?php echo urlencode($currentDir); ?>">
                 <input type="hidden" name="oldName" value="<?php echo htmlspecialchars($target); ?>">
                 <div class="mb-3">
                     <label class="form-label">New Name:</label>
                     <input type="text" name="newName" class="form-control" value="<?php echo htmlspecialchars(basename($target)); ?>" required>
                 </div>
                 <button type="submit" class="btn btn-primary">Rename</button>
             </form>
         </div>
    <?php } ?>
</div>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
