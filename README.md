# URABE-PHP-CRUD-API

`Urabe` is a CRUD and database transaction manager divided in three layers, the first layer is called `KanojoX` acts as a connection manager, wraps the `php-resources` most used functions such as `connect`, `close_connection`,`query`, `fecth_assoc` and `error`. Currently the supported drivers are _ORACLE_, _PG_ and _MYSQL_, each driver is associated with a `KanojoX` class, `ORACLEKanojoX`,`PGKanojoX` and `MYSQLKanojoX`. To learn more about the use of `KanojoX` visit the wiki[[1](https://github.com/ANamelessWolf/urabe/wiki/KanojoX,-The-database-connector)].

The second layer is called `Urabe`, this layer is created from a `KanojoX` object and wraps most common SQL functions allowing to work transparently between database without changing our code. The function include alias for selecting data, updating, deleting, inserting or other query execution. To learn more about the use of `Urabe` visit the wiki[[2](https://github.com/ANamelessWolf/urabe/wiki/Urabe-Class,-Introduction)].

The last layer is called `HasamiWrapper`, this layer manage the CRUD request using `Urabe` as the database manager and the `WebServiceContent` class as the request content. Currently supported verbose `GET`,`POST`,`UPDATE`,`DELETE`. To learn more about the use of `Urabe` visit the wiki[[3]()].


