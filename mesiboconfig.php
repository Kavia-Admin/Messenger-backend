<?php

/*
 Table schema for messages, for edit/delete support:

 CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    `from` INT,
    `to` INT,
    message TEXT,
    sent_at INT,
    is_deleted TINYINT DEFAULT 0,
    deleted_at INT NULL,
    is_edited TINYINT DEFAULT 0,
    edited_at INT NULL
 );
*/

$apptoken = 'GET IT FROM MESIBO CONSOLE';
