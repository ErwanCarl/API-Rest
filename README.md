# P7 Openclassrooms - API Rest - Erwan Carlini

<!-- [![SymfonyInsight](https://insight.symfony.com/projects/00c7f3e9-6c00-46bf-be2e-77835a3f9bd7/big.svg)](https://insight.symfony.com/projects/00c7f3e9-6c00-46bf-be2e-77835a3f9bd7) -->
---------------

## Starting project

### Project

Creation of an API Rest for an imaginary customer BileMo who needs to expose several API to share his catalog to his Marketplace partners.  
This API also permits to create, read, update and delete the Marketplaces's customers.  
A full documentation is available in order to properly use the API. The API user will also be helped by the implementation of Hypermedia Controls, the third level of Richarson Model.  

### Requirements

- PHP : ⩾ 8.1.0 
- MySQL ⩾ 8.0.30
- Composer
- Symfony 6.2
- Symfony CLI

### Packages Installation

First, clone project and place the project in a new folder, then install all composer packages with command line : ``composer install``.  

### Database datas

First, you will need to change the value of DATABASE_URL in the file .env to match with your database parameters, then create your database Snowtricks.  

To get all necessaries datas :  
* Run ``symfony console doctrine:database:create`` in command to create your database  
* Run ``symfony console doctrine:migration:migrate`` in command to create your tables in your DB from the entities files  
* Run ``php bin/console doctrine:fixtures:load`` to get the basic datas of this project  
* Run ``symfony serve -d`` to use symfony CLI server  

### Lexik JWT Configuration

Most of the configuration in this project doesn't need any change for the API to work properly. Nevertheless you'll need to add a few configuration by yourselves in order to use Lexik JWT.  
First, you will you have to create the public.pem and private.pem file in the config/jwt folder by using the following lines in the terminal :  
* openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096  
* openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout  
  
Don't forget the password it ask you for, you will need it for the JWT_PASSPHRASE configuration to put in your .env file :  
JWT_PASSPHRASE= *your_password*  

### Authentication  
  
In order to authenticate yourselves, you will need either add your own user (i.e marketplace) in the datas fixtures or directly in database, or use one of the created users (i.e marketplace) and authenticate thanks to his username and passsword according to the documentation. The recovered Token is to be used in the request headers as a pair value Authorization (Key) / "Bearer *Token*" (Value) for each request you'll make. The token has an expiration date and you'll need to authenticate again once it expired.  

### Nelmio API Documentation  

You can access to the full detailled documentation by simply adding at the end of your local url : "/api/doc".  

## Libraries list

* Symfony  
* Doctrine  
* Twig 
* JMS serializer
* LekixJWT
* Knp Paginator
* Willdurand Hateoas bundle
* Faker
* Nelmio 
* Security
