<?php


class Comunicacion{
    protected $oConn;
    function __construct($credenciales) {
        $this->oConn = $this->crearConexion($credenciales);
    }
    function crearConexion($credenciales) {
        try {
            $options = [
                PDO::MYSQL_ATTR_LOCAL_INFILE => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
            ];
            return $oConn = new PDO($credenciales["DSN"], $credenciales["USER"], $credenciales["PASSWORD"],$options);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
    function cambiarConexion($credenciales) {
        $this->oConn= $this->crearConexion($credenciales);
    }
}

