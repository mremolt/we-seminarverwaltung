<?php

namespace library;
use library\Database;
use \PDO;

/**
 * Basisklasse für alle Datenbank-Modelle
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package library
 */
abstract class ActiveRecord
{
    protected $id = 0;
    protected $errors = array();
    
    protected static $_tableName = '';

    /**
     * Konstruktor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->fromArray($data);
    }

    /**
     * Gibt eine kurze Beschreibung des Objekts zurück
     * 
     * @return string
     */
    public function __toString()
    {
        return get_called_class() .  ': ' . $this->getId();
    }

    /**
     * Gibt die ID zurück
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setzt die ID
     * 
     * @param string $id
     * @return ActiveRecord
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Gibt die Datenbank-Attribute dieses Exemplars zurück.
     * 
     * @param boolean $withId Soll auch das Attribut ID mit zurückgegeben werden?
     * @return array $values
     */
    public function toArray($withId = true)
    {
        $values = array();
        foreach (static::getTableColumns($withId) as $column) {
            $getterName = 'get' . ucfirst($column);
            $values[$column] = $this->$getterName();
        }
        return $values;
    }

    public function fromArray(array $data = array())
    {
        if ($data) {
            foreach ($data as $k => $v) {
                $setterName = 'set' . ucfirst($k);
                // wenn ein ungültiges Attribut übergeben wurde (ohne Setter), ignoriere es
                if (method_exists($this, $setterName)) {
                    $this->$setterName($v);
                }
            }
        }
    }

    /**
     * Gibt alle aufgetretenen Fehler zurück
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Erzeugt einen neuen Fehler
     *
     * @param string $attributeName
     * @param string $message
     * @return ActiveRecord
     */
    public function addError($attributeName, $message)
    {
        $this->errors[$attributeName] = $message;
        return $this;
    }

    /**
     * Sind beim Setzen der Attribute Fehler aufgetreten?
     *
     * @return boolean
     */
    public function isValid()
    {
        return count($this->getErrors()) === 0;
    }
    
    /**
     * Speichert das Exemplar in der Datenbank
     *
     * Je nachdem, ob das Attribut ID einen Wert größer als 0 hat, also in der
     * Datenbank als Eintrag existiert, wird die Methode _insert() oder _update()
     * aufgerufen.
     *
     * @return ActiveRecord
     */
    public function save()
    {
        $this->_preSave();

        // nur speichern, wenn alle Daten im Objekt valide sind
        if ($this->isValid()) {
            if ($this->getId() > 0) {
                $this->_update();
            } else {
                $this->_insert();
            }
        }

        $this->_postSave();
        return $this;
    }

    /**
     * Löscht das Exemplar aus der Datenbank
     * 
     * @return ActiveRecord
     */
    public function delete()
    {
        $this->_preDelete();

        // nur löschen, wenn das Objekt auch schon gespeichert ist
        if ($this->getId() > 0) {
            $sql = 'DELETE FROM ' . static::getTableName() . ' WHERE id = ?';
            $statement = Database::getInstance()->prepare($sql);
            $statement->execute(array( $this->getId() ));
            // markiere das Objekt als nicht in der Datenbank gespeichert
            $this->setId(0);
        }

        $this->_postDelete();
        return $this;
    }

    /**
     * Gibt den Datensatz mit der passenden ID als Objekt zurück
     * 
     * @param integer $id
     * @return ActiveRecord
     */
    public static function find($id)
    {
        $sql = 'SELECT * FROM ' . static::getTableName() . ' WHERE id = ?';
        $statement = Database::getInstance()->prepare($sql);
        $statement->execute(array($id));
        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $statement->fetch();
    }

    /**
     * Gibt alle Datensätze der verwalteten Tabelle als Objekte zurück
     * 
     * @return array
     */
    public static function findAll()
    {
        $sql = 'SELECT * FROM ' . static::getTableName();
        
        $statement = Database::getInstance()->query($sql);
        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $statement->fetchAll();
    }

    public static function findBy($attribute, $value, $operator = '=')
    {
        // nur ausführen, wenn $attribute auch als Spalte in der Tabelle existiert
        if ( in_array($attribute, static::getTableColumns()) ) {
            $sql  = 'SELECT * FROM ' . static::getTableName();
            // $value wird hier nicht per Platzhalter eingefügt, um das SQL flexibler
            // zu halten. Der aufrufende Code ist für das Sichern/Formatieren verantwortlich
            $sql .= ' WHERE ' . $attribute . ' ' . $operator . ' ' . $value;

            $statement = Database::getInstance()->prepare($sql);
            $statement->execute(array($value));
            $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            return $statement->fetchAll();
        }
    }

    /**
     * Zählt die In der Tabelle gespeicherten Datensätze
     * 
     * @return integer
     */
    public static function count()
    {
        $sql = 'SELECT COUNT(id) AS count FROM ' . static::getTableName();
        $statement = Database::getInstance()->query($sql);
        $data = $statement->fetch();
        return $data['count'];
    }

    /**
     * Zählt die In der Tabelle gespeicherten Datensätze
     *
     * @return integer
     */
    public static function countBy($attribute, $value, $operator = '=')
    {
        // nur ausführen, wenn $attribute auch als Spalte in der Tabelle existiert
        if ( in_array($attribute, static::getTableColumns()) ) {
            $sql = 'SELECT COUNT(id) AS count FROM ' . static::getTableName();
            $sql .= ' WHERE ' . $attribute . $operator . '?';

            $statement = Database::getInstance()->prepare($sql);
            $statement->execute(array($value));
            $data = $statement->fetch();
            return $data['count'];
        }
    }

    /**
     * Gibt den Namen der Datenbank-Tabelle zurück, die diese Klasse verwaltet
     * 
     * @return string $tableName
     */
    public static function getTableName()
    {
        if (static::$_tableName) {
            $tableName = static::$_tableName;
        } else {
            $className = static::_stripNamespace(get_called_class());
            $tableName = strtolower($className);
        }
        return $tableName;
    }

    /**
     * Gibt die Spalten der verwalteten Tabelle zurück
     * 
     * Die Methode liest die tatsächliche Struktur der Tabelle aus (per SQL)
     * und verwendet die gefundenen Spaltennamen.
     * 
     * @param boolean $withId Soll auch die Spalte ID zurückgegeben werden?
     * @return array $columns
     */
    public static function getTableColumns($withId = true)
    {
        $sql = 'SHOW COLUMNS from ' . static::getTableName();
        if (! $withId) {
            // hole alle Spaltennamen außer id
            $sql .= ' WHERE Field != "id"';
        }
        $statement = Database::getInstance()->query($sql);

        $columns = array();
        foreach ($statement->fetchAll() as $row) {
            // hole nur die Feldnamen (Field-spalte) aus den Datensätzen
            $columns[] = $row['Field'];
        }
        return $columns;
    }

    /**
     * Speichert das Objekt als neuen Eintrag in die verwaltete Tabelle
     */
    protected function _insert()
    {
        $this->_preInsert();

        $columns = static::getTableColumns(false);
        $columnSql = implode(', ', $columns);
        // Die benannten Parameter in SQL benötigen einen : vor dem Namen,
        // also müssen wir das im implode() einbauen
        $parameterSql = ':' . implode(', :', $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            static::getTableName(),
            $columnSql,
            $parameterSql
        );
        
        $db = Database::getInstance();
        $statement = $db->prepare($sql);
        // nicht die ID ins Array aufnehmen!
        $statement->execute($this->toArray(false));
        
        // da das Objekt nun gespeichert ist, muss auch das Attribut ID gesetzt sein.
        $this->setId(intval($db->lastInsertId()));

        $this->_postInsert();
    }

    /**
     * Aktualisiert den Datensatz in der verwalteten Tabelle
     */
    protected function _update()
    {
        $this->_preUpdate();

        $columns = static::getTableColumns(false);

        $columnStatements = array();
        foreach ($columns as $column) {
            $columnStatements[] = sprintf('%s = :%s', $column, $column);
        }
        $columnSql = implode(', ', $columnStatements);

        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id',
            static::getTableName(),
            $columnSql
        );

        $statement = Database::getInstance()->prepare($sql);
        $statement->execute($this->toArray());

        $this->_postUpdate();
    }

    /**
     * Hook-Methode
     */
    protected function _preSave()
    {

    }

    /**
     * Hook-Methode
     */
    protected function _postSave()
    {

    }

    /**
     * Hook-Methode
     */
    protected function _preInsert()
    {

    }

    /**
     * Hook-Methode
     */
    protected function _postInsert()
    {

    }

    /**
     * Hook-Methode
     */
    protected function _preUpdate()
    {

    }

    /**
     * Hook-Methode
     */
    protected function _postUpdate()
    {

    }

    /**
     * Hook-Methode
     */
    protected function _preDelete()
    {

    }

    /**
     * Hook-Methode
     */
    protected function _postDelete()
    {

    }

    /**
     * Entfernt das Namespace-Prefix vom Klassennamen
     *
     * Diese Helfermethode wird verwendet, wenn nur der Klassenname ohne Namespace
     * benötigt wird (z.B. Errechnung des Tabellennamens).
     *
     * @param string $className
     * @return string $strippedClassName
     */
    protected static function _stripNamespace($className)
    {
        $parts = explode('\\', $className);
        // der Klassenname ist das letzte Element im Array
        return array_pop($parts);
    }
}