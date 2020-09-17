<?php
class InsertarManga extends Comunicacion
{
    public $stmtInsertar;
    public $stmtSelect;
    public function __construct($credenciales)
    {
        parent::__construct($credenciales);
        $this->stmtInsertar = $this->oConn->prepare("INSERT INTO `mangas` (`name`,`imageInfo`,`alternativeTitle`,`author`,`artist`,`genre`,`type`,`status`,`synopsis`,`chapters`) VALUES (?,?,?,?,?,?,?,?,?,?)");
    }
    public function insertar($datos)
    {
        try {
            $this->stmtInsertar->execute([$datos["name"], $datos["imageInfo"], $datos["alternativeTitle"], $datos["author"], $datos["artist"], $datos["genre"], $datos["type"], $datos["status"], $datos["synopsis"], $datos["chapters"]]);
            //echo $datos["name"] . " insertado con éxito".PHP_EOL;
        } catch (Exception $e) {
            var_dump($datos);
            var_dump($e->getMessage());
            echo PHP_EOL;
        }
    }
    public function select()
    {
        $status = $this->oConn->getAttribute(PDO::ATTR_CONNECTION_STATUS);
        var_dump($status);
        $this->stmtSelect->execute();
        $results = $this->stmtSelect->fetch();
        var_dump($results);
    }
}
