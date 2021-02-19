<?php

// инициализация работы с базой данных через библиотеку pdo

$pdo = PDO::getAvailableDrivers();
if (!in_array($db -> type, $pdo)) {
	die( $dberrors -> block . $dberrors -> unsupport -> $currlang );
}

$connect = dbPDOConnect();

?>