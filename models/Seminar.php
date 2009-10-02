<?php

namespace models;
use library\ActiveRecord;

/**
 * Ein Seminar
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package models
 */
class Seminar extends ActiveRecord
{
    protected static $_tableName = 'seminare';

    protected $titel;
    protected $beschreibung;
    protected $preis;
    protected $kategorie;

    /**
     * Getter
     * 
     * @return string
     */
    public function getTitel()
    {
        return $this->titel;
    }

    /**
     * Setter
     *
     * @param string $titel
     * @return Seminar
     */
    public function setTitel($titel)
    {
        if (empty($titel)) {
            $this->addError('titel', 'Das Feld Titel darf nich leer sein');
        }
        $this->titel = $titel;
        return $this;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getBeschreibung()
    {
        return $this->beschreibung;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getBeschreibungFormatiert()
    {
        return nl2br($this->beschreibung);
    }

    /**
     * Setter
     *
     * @param string $beschreibung
     * @return Seminar
     */
    public function setBeschreibung($beschreibung)
    {
        $this->beschreibung = $beschreibung;
        return $this;
    }

    /**
     * Getter
     *
     * @return double
     */
    public function getPreis()
    {
        return $this->preis;
    }

    /**
     * Setter
     *
     * @param double $preis
     * @return Seminar
     */
    public function setPreis($preis)
    {
        $preis = floatval($preis);
        if ($preis <= 0.0) {
            $this->addError('preis', 'Der Preis muss ein positiver Zahlenwert sein.');
        }
        $this->preis = $preis;

        return $this;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getKategorie()
    {
        return $this->kategorie;
    }

    /**
     * Setter
     * 
     * @param string $kategorie
     * @return Seminar
     */
    public function setKategorie($kategorie)
    {
        if (empty($kategorie)) {
            $this->addError('kategorie', 'Das Feld Kategorie darf nich leer sein');
        }
        $this->kategorie = $kategorie;
        return $this;
    }

    /**
     * Getter
     * 
     * @return array
     */
    public function getSeminartermine()
    {
        return Seminartermin::findBy('seminar_id', $this->getId());
    }

    /**
     * Setter
     *
     * Dem übergebenen Seminartermin wird das aktuelle Seminar zugewiesen und das
     * Objekt auch gleich gespeichert, wenn $save auf true steht.
     *
     * @param Seminartermin $seminartermin
     * @param boolean $save
     * @return Seminar
     */
    public function addSeminartermin(Seminartermin $seminartermin, $save = true)
    {
        $seminartermin->setSeminar($this);
        if ($save) {
            $seminartermin->save();
        }
        return $this;
    }

    /**
     * Zählt die zugeordneten Seminartermine
     * @return integer
     */
    public function countSeminartermine()
    {
        return Seminartermin::countBy('seminar_id', $this->getId());
    }

    /**
     * Gibt eine kurze Beschreibung des Objekts zurück
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTitel() . ' (Preis: ' . $this->getPreis() . ' €)';
    }
}