<?php
class Task {
    private $title;
    private $priority;

    public function __construct($title, $priority) {
        $this->setTitle($title);
        $this->setPriority($priority);
    }

    public function setTitle($title) {
        if (trim($title) === '') {
            throw new Exception("Titel mag niet leeg zijn.");
        }
        $this->title = trim($title);
    }

    public function setPriority($priority) {
        $toegestanePrioriteiten = ['laag', 'gemiddeld', 'hoog'];
        if (!in_array($priority, $toegestanePrioriteiten)) {
            throw new Exception("Ongeldige prioriteit opgegeven.");
        }
        $this->priority = $priority;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getPriority() {
        return $this->priority;
    }
}
