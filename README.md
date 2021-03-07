# MsgReceiveAndStore

## Introduction

This project is about retrieving, filtering and storing a defined data model into a MySQL Database.  
Specifically a web server has been implemented using [Symfony 4.4](https://symfony.com/doc/4.4/index.html).  
An API has been created to trigger the execution of the pipeline [GET -> Filter -> Store].  
The filtering of the received data is conducted to an external RabbitMQ Queue after publishing the message.  
We subscribe to a "results" Queue to get the filtered message and store it to the MySQL DB  

## Prerequisites

[PHP v7.4.16](https://www.php.net/archive/2021.php#2021-03-04-1)

## Installation

#### Clone Repo and install packages

`git clone https://github.com/agtzdimi/MsgReceiveAndStore`  
`cd MsgReceiveAndStore`  
`composer install`  

#### Database schema and tables creation
`php bin/console make:migration`  
`php bin/console doctrine:migrations:migrate`  

#### Start Server
`symfony server:start`  
Navigate to:  
`http://localhost:8001/messages/getNewMessage`    
or    
`curl http://localhost:8001/messages/getNewMessage`  

## Database Scheme

####Table: messages  
**Fields**  
`id int(11) NULLABLE NO AUTO_INC`  
`gateway_eui decimal(19) NULLABLE NO`  
`profile_id decimal(19) NULLABLE NO`  
`endpoint_id decimal(19) NULLABLE NO`  
`cluster_id decimal(19) NULLABLE NO`  
`attribute_id decimal(19) NULLABLE NO`  
`value  int(11) NULLABLE NO`  
`timestamp  int(11) NULLABLE NO`  

## Environment file

#### RabbitMQ Conf

- RABBITMQ_HOSTNAME=<rabbitMQ_Host>  
- RABBITMQ_USERNAME=<rabbitMQ_Username>  
- RABBITMQ_PASSWORD=<rabbitMQ_Password>  
- RABBITMQ_QUEUE_NAME=<rabbitMQ_Queue_Name_To_Subscribe>  
- RABBITMQ_EXCHANGE=<rabbitMQ_Exchange>  
- RABBITMQ_PORT=<rabbitMQ_Port>  
- RABBITMQ_TIMEOUT=<rabbitMQ_Timeout_To_Wait_Filtered_Message>  

#### MySQL Conf
- DATABASE_URL=mysql://<username>:<password>@<host>:<port>/<db_name>  
- MYSQL_HOSTNAME=<MySQL_Host>  
- MYSQL_USERNAME=<rMySQL_Username>  
- MYSQL_PASSWORD=<MySQL_Password>  
- MYSQL_DB_NAME=<MySQL_DB_NAME>  

#### External API
RESULTS_URL=<external_API>

## Acknowledgments

[Symfony docs for Installation](https://symfony.com/doc/current/setup.html)  
[Symfony Doctrine docs](https://symfony.com/doc/current/doctrine.html)  
[RabbitMQ Usage](https://www.rabbitmq.com/tutorials/tutorial-five-php.html)  
[Deserialize example article](https://medium.com/infostud/using-symfony-serializer-to-consume-rest-apis-in-oop-way-9c5de319ef7b)  

## Build With
[PHP Storm](https://www.jetbrains.com/phpstorm/promo/?gclid=CjwKCAiAkJKCBhAyEiwAKQBCkjHBgaGK1XIupqE2f7ygHqZVo49OGbdm7fLSH38cmzV1QBipopme8hoCAe4QAvD_BwE)

## Authors

- Dimitrios Agtzidis
