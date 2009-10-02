<?php

namespace models;
use library\Database, library\ActiveRecord, library\DateTime;
use \PDO, \Exception;

/**
 * Ein Seminartermin
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package models
 */
class Seminartermin extends ActiveRecord
{
    protected static $_tableName = 'seminartermine';

    protected $beginn;
    protected $ende;
    protected $raum;
    protected $seminar_id;

    /**
     * Getter
     *
     * @return DateTime
     */
    public function getBeginn()
    {
        try {
            $beginn = new DateTime($this->beginn);
        } catch (Exception $e) {
            $beginn = new DateTime();
        }
        return $beginn;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getBeginnFormatiert()
    {
        return $this->getBeginn()->format('d.m.Y');
    }

    /**
     * Setter
     *
     * @param string $beginn
     * @return Seminartermin
     */
    public function setBeginn($beginn)
    {
        try {
            $beginnDateTime = new DateTime($beginn);
            $this->beginn = $beginn;
        } catch (Exception $e) {
            $this->addError('beginn', 'Das Feld Beginn enthält ein ungültiges Datum');
        }
        return $this;
    }

    /**
     * Getter
     *
     * @return DateTime
     */
    public function getEnde()
    {
        try {
            $ende = new DateTime($this->ende);
        } catch (Exception $e) {
            $ende = new DateTime();
        }
        return $ende;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getEndeformatiert()
    {
        return $this->getEnde()->format('d.m.Y');
    }

    /**
     * Setter
     *
     * @param string $ende
     * @return Seminartermin
     */
    public function setEnde($ende)
    {
        try {
            $endeDateTime = new DateTime($ende);
            $this->ende = $ende;

            $dauer = $this->getBeginn()->diff($endeDateTime);
            if ($dauer->invert === 1) {
                $this->addError('ende', 'Das Feld Ende liegt zeitlich vor dem Beginn');
            }
        } catch (Exception $e) {
            $this->addError('ende', 'Das Feld Ende enthält ein ungültiges Datum');
        }
        return $this;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getRaum()
    {
        return $this->raum;
    }

    /**
     * Setter
     *
     * @param string $raum
     * @return Seminartermin
     */
    public function setRaum($raum)
    {
        if (empty($raum)) {
            $this->addError('raum', 'Das Feld Raum darf nich leer sein');
        }
        $this->raum = $raum;
        return $this;
    }

    /**
     * Gibt die Dauer eines Seminartermins in Tagen aus
     *
     * @return integer
     */
    public function getDauer()
    {
        $beginn = $this->getBeginn();
        $ende = $this->getEnde();

        return $beginn->diff($ende);
    }

    /**
     * Getter
     *
     * @return Seminar
     */
    public function getSeminar()
    {
        return Seminar::find($this->seminar_id);
    }

    /**
     * Setter
     *
     * @param Seminar $seminar
     * @return Seminartermin
     */
    public function setSeminar(Seminar $seminar)
    {
        $this->seminar_id = $seminar->getId();
        return $this;
    }

    /**
     * Gibt die Teilnehmer (Klasse Benutzer) des Seminartermins zurück
     * 
     * @return array
     */
    public function getTeilnehmer()
    {
        return Benutzer::findBySeminartermin($this);
    }

    /**
     * Gibt die Benutzer zurück, nich noch nicht für den Seminartermin gebucht sind
     *
     * @return array
     */
    public function getNichtTeilnehmer()
    {
        return Benutzer::excludeBySeminartermin($this);
    }

    /**
     * Fügt dem Seminartermin einen neuen Teilnehmer (Klasse Benutzer) hinzu.
     *
     * @param Benutzer $teilnehmer
     * @return Seminartermin
     */
    public function addTeilnehmer(Benutzer $teilnehmer)
    {
        // wir können die Zwischentabelle nicht befüllen, so lange der Benutzer
        // nicht gespeichert ist.
        if ( ! $teilnehmer->getId() > 0 ) {
            $teilnehmer->save();
        }

        $sql = 'INSERT INTO nimmt_teil (benutzer_id, seminartermin_id) VALUES (?, ?)';
        $statement = Database::getInstance()->prepare($sql);
        $statement->execute(array( $teilnehmer->getId(), $this->getId() ));

        return $this;
    }

    /**
     * Entfernt einen Teilnehmer aus dem Seminartermin
     *
     * @param Benutzer $teilnehmer
     * @return Seminartermin
     */
    public function removeTeilnehmer(Benutzer $teilnehmer)
    {
        // nur wenn der Teilnehmer gespeichert ist, macht das löschen Sinn
        if ( $teilnehmer->getId() > 0 ) {
            $sql = 'DELETE FROM nimmt_teil WHERE benutzer_id = ? AND seminartermin_id = ?';
            $statement = Database::getInstance()->prepare($sql);
            $statement->execute(array( $teilnehmer->getId(), $this->getId() ));
        }
        return $this;
    }

    /**
     * Zählt die Teilnehmer eines Seminartermins
     *
     * @return integer
     */
    public function countTeilnehmer()
    {
        // TODO: Lösung, die weniger Performance verschwendet
        return count($this->getTeilnehmer());
    }

    /**
     * Gibt eine kurze Beschreibung des Objekts zurück
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '"%s" von %s bis %s',
            $this->getSeminar()->getTitel(),
            $this->getBeginnFormatiert(),
            $this->getEndeFormatiert()
        );
    }

    /**
     * Findet Seminartermine eines Benutzers über die Zwischentabelle nimmt_teil
     *
     * Hier ist ein SQL JOIN notwendig, wobei die Spalten der Zwischentabelle nicht
     * im Objekt auftauchen sollen, also kein SELECT *.
     *
     * @param Benutzer $benutzer
     * @return array
     */
    public static function findByBenutzer(Benutzer $benutzer)
    {
        $sql = sprintf(
            'SELECT %s FROM seminartermine st JOIN nimmt_teil nt ON st.id = nt.seminartermin_id WHERE nt.benutzer_id = ?',
            implode(', ', static::getTableColumns())  
        );
        $statement = Database::getInstance()->prepare($sql);
        $statement->execute(array( $benutzer->getId() ));
        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $statement->fetchAll();
    }

    // diese Zugriffsmethoden sind nur zur internen Verwendung

    /**
     * Getter
     *
     * @return integer
     */
    protected function getSeminar_id()
    {
        return intval($this->seminar_id);
    }

    /**
     * Setter
     *
     * @param integer $seminar_id
     * @return Seminartermin
     */
    protected function setSeminar_id($seminar_id)
    {
        $this->seminar_id = intval($seminar_id);
        return $this;
    }
}