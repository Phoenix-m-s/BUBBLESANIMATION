<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titles'])) {
    $titles = $_POST['titles'];
}
