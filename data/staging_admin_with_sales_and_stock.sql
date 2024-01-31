CREATE TABLE modules(
    id int(2) PRIMARY KEY AUTO_INCREMENT,
    name varchar(64), 
    description varchar(128)
);

ALTER TABLE `groups` ADD `module_id` INT(2) NULL AFTER `description`;

INSERT INTO `modules`(`name`,`description`)VALUES
("money","Money"),
("manpower","Manpower"),
("material","Material"),
("machine","Machine"),
("method","Method"),
("marketing","Marketing"),
("management","Management")

INSERT INTO `groups`(`name`, `description`, `module_id`)VALUES
#MONEY MODULE
("cashier", "Cashier", 1), 
("tc", "Team Captain", 1),
("manager", "Manager", 1),

#MATERIAL
("store staff", "Store Staff", 3), 
("store manager", "Store Manager", 3), 
("procurement", "Procurement", 3), 
("supplier", "Supplier", 3),
("operations", "Operations", 3),
("admin", "Admin", 3),
("supplier dispatch", "Supplier Dispatch", 3),
("finance", "Finance", 3),
("supplier finance", "Supplier Finance", 3);

INSERT INTO `groups` (`id`, `name`, `description`, `module_id`) VALUES ('17', 'members', 'Members', NULL)

ALTER TABLE `companies` CHANGE `name` `company_name` VARCHAR(265) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

ALTER TABLE `users_module` CHANGE `module_id` `module_id` MEDIUMINT(8) UNSIGNED NULL;