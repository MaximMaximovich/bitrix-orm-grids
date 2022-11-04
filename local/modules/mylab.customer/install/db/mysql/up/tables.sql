CREATE TABLE `y_addresses`
(
    `ID`                   INT          NOT NULL AUTO_INCREMENT,
    `CUSTOMER_ID`          INT          NOT NULL,
    `ADDRESS_LINE`         VARCHAR(100) NOT NULL,
    `ADDRESS_TYPE`         VARCHAR(8)   NOT NULL,
    `CITY`                 VARCHAR(50)  NOT NULL,
    `POSTAL_CODE`          VARCHAR(6)   NOT NULL,
    `REGION`               VARCHAR(20)  NOT NULL,
    `COUNTRY`              VARCHAR(7)   NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE `y_customers`
(
    `ID`                   INT          NOT NULL AUTO_INCREMENT,
    `FIRST_NAME`           VARCHAR(50)  NOT NULL,
    `LAST_NAME`            VARCHAR(50)  NOT NULL,
    `PHONE`                VARCHAR(15)  NOT NULL,
    `EMAIL`                VARCHAR(255) NOT NULL,
    PRIMARY KEY (`ID`)
);