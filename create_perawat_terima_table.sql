-- SQL untuk membuat tabel perawat_terima (dijalankan manual di database darah)
-- Jalankan di: 192.168.7.241 database darah

CREATE TABLE `darah`.`perawat_terima` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NO_PERMINTAAN` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_1` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_2` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_3` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_4` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_5` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_6` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_7` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_8` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_9` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_10` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_11` varchar(50) DEFAULT NULL,
  `PERAWAT_TERIMA_12` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;