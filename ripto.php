<?php
// =============================================
// সম্পূর্ণ ফাইল ম্যানেজার - রুট ডিরেক্টরি অ্যাক্সেস সহ
// =============================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// =============================================
// কনফিগারেশন
// =============================================
// রুট ডিরেক্টরি - যেখানে এই ফাইল আছে সেখান থেকে পুরো সাইট দেখা যাবে
$root_dir = $_SERVER['DOCUMENT_ROOT'];
$current_dir = isset($_GET['dir']) ? $_GET['dir'] : $root_dir;
$current_dir = realpath($current_dir);

// নিরাপত্তা: রুটের বাইরে যেতে দেবেন না
if ($current_dir === false || strpos($current_dir, $root_dir) !== 0) {
    $current_dir = $root_dir;
}

// ভাষা
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'bn';
$lang_text = [
    'bn' => [
        'title' => 'ফাইল ম্যানেজার - cPanel স্টাইল',
        'upload' => 'ফাইল আপলোড',
        'new_folder' => 'নতুন ফোল্ডার',
        'new_file' => 'নতুন ফাইল',
        'name' => 'নাম',
        'size' => 'সাইজ',
        'modified' => 'পরিবর্তন',
        'actions' => 'অ্যাকশন',
        'edit' => 'সম্পাদনা',
        'delete' => 'মুছুন',
        'rename' => 'নাম পরিবর্তন',
        'download' => 'ডাউনলোড',
        'back' => 'পেছনে',
        'parent' => 'প্যারেন্ট ডিরেক্টরি',
        'select_lang' => 'ভাষা',
        'save' => 'সংরক্ষণ',
        'cancel' => 'বাতিল',
        'file_edited' => 'ফাইল সম্পাদিত হয়েছে!',
        'file_deleted' => 'ফাইল মুছে ফেলা হয়েছে!',
        'folder_deleted' => 'ফোল্ডার মুছে ফেলা হয়েছে!',
        'file_uploaded' => 'ফাইল আপলোড হয়েছে!',
        'folder_created' => 'ফোল্ডার তৈরি হয়েছে!',
        'file_created' => 'ফাইল তৈরি হয়েছে!',
        'renamed' => 'নাম পরিবর্তন সফল!',
        'replace_success' => 'ফাইল রিপ্লেস হয়েছে!',
        'error' => 'ত্রুটি!',
        'confirm_delete' => 'আপনি কি নিশ্চিত?',
        'path' => 'পাথ',
        'root' => 'রুট ডিরেক্টরি',
        'select_file' => 'ফাইল নির্বাচন করুন',
    ],
    'en' => [
        'title' => 'File Manager - cPanel Style',
        'upload' => 'Upload File',
        'new_folder' => 'New Folder',
        'new_file' => 'New File',
        'name' => 'Name',
        'size' => 'Size',
        'modified' => 'Modified',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'rename' => 'Rename',
        'download' => 'Download',
        'back' => 'Back',
        'parent' => 'Parent Directory',
        'select_lang' => 'Language',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'file_edited' => 'File successfully edited!',
        'file_deleted' => 'File deleted!',
        'folder_deleted' => 'Folder deleted!',
        'file_uploaded' => 'File uploaded!',
        'folder_created' => 'Folder created!',
        'file_created' => 'File created!',
        'renamed' => 'Rename successful!',
        'replace_success' => 'File replaced!',
        'error' => 'Error!',
        'confirm_delete' => 'Are you sure?',
        'path' => 'Path',
        'root' => 'Root Directory',
        'select_file' => 'Select File',
    ]
];
$t = $lang_text[$lang];

// =============================================
// অ্যাকশন হ্যান্ডলার
// =============================================

// 1. ফাইল এডিট
if (isset($_POST['edit_file'])) {
    $file = $_POST['file_path'];
    $content = $_POST['content'];
    if (file_put_contents($file, $content)) {
        echo "<script>alert('{$t['file_edited']}'); window.location.href='?dir=" . urlencode(dirname($file)) . "&lang=$lang';</script>";
    }
    exit;
}

// 2. ফাইল/ফোল্ডার ডিলিট
if (isset($_GET['delete'])) {
    $file = $_GET['delete'];
    if (is_file($file)) {
        unlink($file);
    } elseif (is_dir($file)) {
        rmdir($file);
    }
    header('Location: ?dir=' . urlencode(dirname($file)) . '&lang=' . $lang);
    exit;
}

// 3. ফাইল আপলোড (নতুন বা রিপ্লেস)
if (isset($_FILES['upload_file'])) {
    $target = $current_dir . '/' . basename($_FILES['upload_file']['name']);
    if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $target)) {
        echo "<script>alert('{$t['file_uploaded']}'); window.location.href='?dir=" . urlencode($current_dir) . "&lang=$lang';</script>";
    }
    exit;
}

// 4. ফোল্ডার তৈরি
if (isset($_POST['create_folder'])) {
    $folder_name = $_POST['folder_name'];
    if (!empty($folder_name)) {
        mkdir($current_dir . '/' . $folder_name);
        echo "<script>alert('{$t['folder_created']}'); window.location.href='?dir=" . urlencode($current_dir) . "&lang=$lang';</script>";
    }
    exit;
}

// 5. ফাইল তৈরি
if (isset($_POST['create_file'])) {
    $file_name = $_POST['file_name'];
    if (!empty($file_name)) {
        file_put_contents($current_dir . '/' . $file_name, '');
        echo "<script>alert('{$t['file_created']}'); window.location.href='?dir=" . urlencode($current_dir) . "&lang=$lang';</script>";
    }
    exit;
}

// 6. রিনেম
if (isset($_POST['rename_item'])) {
    $old = $_POST['old_name'];
    $new = $_POST['new_name'];
    if (!empty($new)) {
        rename($old, dirname($old) . '/' . $new);
        echo "<script>alert('{$t['renamed']}'); window.location.href='?dir=" . urlencode(dirname($old)) . "&lang=$lang';</script>";
    }
    exit;
}

// 7. ডাউনলোড
if (isset($_GET['download'])) {
    $file = $_GET['download'];
    if (file_exists($file) && is_file($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}

// =============================================
// ফাইল লিস্ট তৈরি
// =============================================
$files = scandir($current_dir);
$file_list = [];
foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    $path = $current_dir . '/' . $file;
    $file_list[] = [
        'name' => $file,
        'path' => $path,
        'is_dir' => is_dir($path),
        'size' => is_file($path) ? formatSize(filesize($path)) : '-',
        'modified' => date('Y-m-d H:i:s', filemtime($path)),
        'perms' => substr(sprintf('%o', fileperms($path)), -4),
    ];
}

function formatSize($bytes) {
    if ($bytes == 0) return '0 B';
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

// ফাইল কন্টেন্ট লোড (AJAX)
if (isset($_GET['get_content'])) {
    $file = $_GET['get_content'];
    if (file_exists($file) && is_file($file)) {
        echo file_get_contents($file);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['title'] ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #eef2f7; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.08); overflow: hidden; }
        
        /* হেডার */
        .header { background: linear-gradient(135deg, #1a2332 0%, #2c3e50 100%); padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 600; }
        .header h1 i { color: #f1c40f; margin-right: 10px; }
        .header-right { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .header-right a { color: #fff; text-decoration: none; padding: 6px 14px; border-radius: 6px; font-size: 13px; transition: all 0.3s; background: rgba(255,255,255,0.1); }
        .header-right a:hover { background: rgba(255,255,255,0.25); }
        .header-right a.active { background: #3498db; }
        
        /* পাথ বার */
        .path-bar { background: #f8f9fa; padding: 12px 30px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .path-bar .path { font-size: 14px; color: #495057; word-break: break-all; }
        .path-bar .path i { color: #3498db; margin-right: 8px; }
        .path-bar .path strong { color: #2c3e50; }
        
        /* টুলবার */
        .toolbar { background: #fff; padding: 15px 30px; border-bottom: 1px solid #e9ecef; display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        .toolbar form { display: flex; gap: 6px; align-items: center; background: #f8f9fa; padding: 4px 10px 4px 14px; border-radius: 8px; border: 1px solid #e9ecef; }
        .toolbar form input[type="text"] { border: none; background: transparent; padding: 6px 0; font-size: 13px; outline: none; width: 120px; }
        .toolbar form input[type="file"] { border: none; padding: 4px 0; font-size: 12px; max-width: 120px; }
        .toolbar form button { background: #3498db; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 13px; transition: all 0.3s; }
        .toolbar form button:hover { background: #2980b9; }
        .toolbar form button.green { background: #2ecc71; }
        .toolbar form button.green:hover { background: #27ae60; }
        .toolbar form button.orange { background: #f39c12; }
        .toolbar form button.orange:hover { background: #e67e22; }
        .toolbar .parent-btn { background: #6c757d; color: #fff; padding: 6px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; transition: all 0.3s; }
        .toolbar .parent-btn:hover { background: #5a6268; }
        
        /* টেবিল */
        .table-wrap { padding: 0 20px 20px 20px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { text-align: left; padding: 12px 15px; background: #f8f9fa; color: #495057; font-weight: 600; border-bottom: 2px solid #e9ecef; }
        td { padding: 10px 15px; border-bottom: 1px solid #f1f3f5; vertical-align: middle; }
        tr:hover td { background: #f8f9fa; }
        .file-name { display: flex; align-items: center; gap: 8px; }
        .file-name .icon { font-size: 18px; }
        .file-name .icon.folder { color: #f39c12; }
        .file-name .icon.file { color: #3498db; }
        .file-name .icon.image { color: #e74c3c; }
        .file-name a { color: #2c3e50; text-decoration: none; font-weight: 500; }
        .file-name a:hover { color: #3498db; }
        .actions-cell { display: flex; gap: 4px; flex-wrap: wrap; }
        .actions-cell a { padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; transition: all 0.2s; display: inline-flex; align-items: center; gap: 4px; }
        .actions-cell .edit { background: #2ecc71; color: #fff; }
        .actions-cell .edit:hover { background: #27ae60; }
        .actions-cell .delete { background: #e74c3c; color: #fff; }
        .actions-cell .delete:hover { background: #c0392b; }
        .actions-cell .rename { background: #f39c12; color: #fff; }
        .actions-cell .rename:hover { background: #e67e22; }
        .actions-cell .download { background: #9b59b6; color: #fff; }
        .actions-cell .download:hover { background: #8e44ad; }
        .size-col { color: #6c757d; font-size: 13px; }
        .modified-col { color: #6c757d; font-size: 13px; }
        .empty-row td { text-align: center; padding: 40px; color: #adb5bd; font-size: 16px; }
        
        /* মোডাল */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background: #fff; padding: 30px; border-radius: 12px; max-width: 700px; width: 95%; max-height: 85vh; overflow-y: auto; }
        .modal-content h2 { margin-bottom: 15px; color: #2c3e50; display: flex; align-items: center; gap: 10px; }
        .modal-content textarea { width: 100%; height: 300px; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-family: 'Courier New', monospace; font-size: 14px; resize: vertical; }
        .modal-content input[type="text"] { width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 15px; }
        .modal-actions { display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap; }
        .modal-actions button { padding: 8px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; transition: all 0.3s; }
        .modal-actions .save-btn { background: #2ecc71; color: #fff; }
        .modal-actions .save-btn:hover { background: #27ae60; }
        .modal-actions .cancel-btn { background: #e74c3c; color: #fff; }
        .modal-actions .cancel-btn:hover { background: #c0392b; }
        
        @media (max-width: 768px) {
            .header { flex-direction: column; align-items: stretch; }
            .header-right { justify-content: center; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .toolbar form { flex-wrap: wrap; }
            .toolbar form input[type="text"] { width: 100%; }
            .path-bar { flex-direction: column; align-items: stretch; }
            table, thead, tbody, th, td, tr { display: block; }
            thead { display: none; }
            tr { margin-bottom: 12px; border: 1px solid #e9ecef; border-radius: 8px; padding: 8px 12px; background: #fff; }
            td { border: none; padding: 5px 0; display: flex; justify-content: space-between; align-items: center; }
            td::before { content: attr(data-label); font-weight: 600; color: #495057; margin-right: 15px; }
            .actions-cell { justify-content: flex-end; }
            .actions-cell a { font-size: 11px; padding: 3px 8px; }
            .modal-content { padding: 20px; }
            .modal-content textarea { height: 200px; }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- হেডার -->
    <div class="header">
        <h1><i class="fas fa-folder-open"></i> <?= $t['title'] ?></h1>
        <div class="header-right">
            <a href="?dir=<?= urlencode($root_dir) ?>&lang=bn" class="<?= $lang == 'bn' ? 'active' : '' ?>">বাংলা</a>
            <a href="?dir=<?= urlencode($current_dir) ?>&lang=en" class="<?= $lang == 'en' ? 'active' : '' ?>">English</a>
            <a href="<?= $_SERVER['PHP_SELF'] ?>?dir=<?= urlencode($root_dir) ?>&lang=<?= $lang ?>" style="background:#3498db;"><i class="fas fa-home"></i> <?= $t['root'] ?></a>
        </div>
    </div>
    
    <!-- পাথ -->
    <div class="path-bar">
        <div class="path"><i class="fas fa-location-dot"></i> <strong><?= $t['path'] ?>:</strong> <?= htmlspecialchars($current_dir) ?></div>
        <?php if ($current_dir != $root_dir): ?>
            <a href="?dir=<?= urlencode(dirname($current_dir)) ?>&lang=<?= $lang ?>" class="parent-btn"><i class="fas fa-arrow-up"></i> <?= $t['parent'] ?></a>
        <?php endif; ?>
    </div>
    
    <!-- টুলবার -->
    <div class="toolbar">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="upload_file" required>
            <button type="submit"><i class="fas fa-upload"></i> <?= $t['upload'] ?></button>
        </form>
        <form action="" method="post">
            <input type="text" name="folder_name" placeholder="<?= $t['new_folder'] ?>" required>
            <button type="submit" name="create_folder" class="orange"><i class="fas fa-folder-plus"></i></button>
        </form>
        <form action="" method="post">
            <input type="text" name="file_name" placeholder="<?= $t['new_file'] ?>" required>
            <button type="submit" name="create_file" class="green"><i class="fas fa-file-plus"></i></button>
        </form>
    </div>
    
    <!-- টেবিল -->
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:45%;"><?= $t['name'] ?></th>
                    <th style="width:15%;"><?= $t['size'] ?></th>
                    <th style="width:20%;"><?= $t['modified'] ?></th>
                    <th style="width:20%;"><?= $t['actions'] ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($file_list)): ?>
                    <tr class="empty-row"><td colspan="4">📭 ফোল্ডারটি খালি</td></tr>
                <?php else: ?>
                    <?php foreach ($file_list as $item): ?>
                        <tr>
                            <td data-label="<?= $t['name'] ?>">
                                <div class="file-name">
                                    <?php if ($item['is_dir']): ?>
                                        <span class="icon folder"><i class="fas fa-folder"></i></span>
                                        <a href="?dir=<?= urlencode($item['path']) ?>&lang=<?= $lang ?>"><?= htmlspecialchars($item['name']) ?></a>
                                    <?php else: ?>
                                        <?php 
                                            $ext = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));
                                            $icon = 'fa-file';
                                            if (in_array($ext, ['jpg','jpeg','png','gif','svg'])) $icon = 'fa-file-image';
                                            elseif (in_array($ext, ['mp4','avi','mkv'])) $icon = 'fa-file-video';
                                            elseif (in_array($ext, ['mp3','wav'])) $icon = 'fa-file-audio';
                                            elseif (in_array($ext, ['zip','rar','7z'])) $icon = 'fa-file-archive';
                                            elseif (in_array($ext, ['php','html','css','js'])) $icon = 'fa-file-code';
                                            elseif (in_array($ext, ['pdf'])) $icon = 'fa-file-pdf';
                                        ?>
                                        <span class="icon file"><i class="fas <?= $icon ?>"></i></span>
                                        <span><?= htmlspecialchars($item['name']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td data-label="<?= $t['size'] ?>" class="size-col"><?= $item['size'] ?></td>
                            <td data-label="<?= $t['modified'] ?>" class="modified-col"><?= $item['modified'] ?></td>
                            <td data-label="<?= $t['actions'] ?>">
                                <div class="actions-cell">
                                    <?php if (!$item['is_dir']): ?>
                                        <a href="#" class="edit" onclick="event.preventDefault(); openEditor('<?= htmlspecialchars($item['path']) ?>')"><i class="fas fa-pen"></i> <?= $t['edit'] ?></a>
                                        <a href="?download=<?= urlencode($item['path']) ?>&lang=<?= $lang ?>" class="download"><i class="fas fa-download"></i></a>
                                    <?php endif; ?>
                                    <a href="?delete=<?= urlencode($item['path']) ?>&lang=<?= $lang ?>" class="delete" onclick="return confirm('<?= $t['confirm_delete'] ?>')"><i class="fas fa-trash"></i></a>
                                    <a href="#" class="rename" onclick="event.preventDefault(); openRename('<?= htmlspecialchars($item['path']) ?>', '<?= htmlspecialchars($item['name']) ?>')"><i class="fas fa-pencil"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- এডিট মোডাল -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <h2><i class="fas fa-pen-to-square"></i> <?= $t['edit'] ?></h2>
        <form action="" method="post">
            <input type="hidden" name="file_path" id="editFilePath">
            <textarea name="content" id="editContent"></textarea>
            <div class="modal-actions">
                <button type="submit" name="edit_file" class="save-btn"><i class="fas fa-save"></i> <?= $t['save'] ?></button>
                <button type="button" class="cancel-btn" onclick="closeModal('editModal')"><i class="fas fa-times"></i> <?= $t['cancel'] ?></button>
            </div>
        </form>
    </div>
</div>

<!-- রিনেম মোডাল -->
<div id="renameModal" class="modal">
    <div class="modal-content">
        <h2><i class="fas fa-pencil"></i> <?= $t['rename'] ?></h2>
        <form action="" method="post">
            <input type="hidden" name="old_name" id="oldName">
            <input type="text" name="new_name" id="newName" placeholder="<?= $t['name'] ?>" required>
            <div class="modal-actions">
                <button type="submit" name="rename_item" class="save-btn"><i class="fas fa-save"></i> <?= $t['save'] ?></button>
                <button type="button" class="cancel-btn" onclick="closeModal('renameModal')"><i class="fas fa-times"></i> <?= $t['cancel'] ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditor(filePath) {
    fetch('?get_content=' + encodeURIComponent(filePath) + '&lang=<?= $lang ?>')
        .then(res => res.text())
        .then(data => {
            document.getElementById('editFilePath').value = filePath;
            document.getElementById('editContent').value = data;
            document.getElementById('editModal').style.display = 'flex';
        });
}

function openRename(filePath, fileName) {
    document.getElementById('oldName').value = filePath;
    document.getElementById('newName').value = fileName;
    document.getElementById('renameModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>
</body>
</html>