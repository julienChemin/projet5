<?php

namespace Chemin\ArtSchool\Model;

class HistoryEntry
{
	private $id,
			$idSchool,
			$category,
			$entry,
			$dateEntry;

	public function __construct(array $data = null)
	{
		if (!empty($data)) {
			$this->hydrate($data);
		}
	}

	public function hydrate(array $data)
	{
		foreach ($data as $key => $value){
			$method = 'set' . ucfirst($key);

			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}

	//GETTERS
	public function getId()
	{
		return $this->id;
	}

	public function getIdSchool()
	{
		return $this->idSchool;
	}

	public function getCategory()
	{
		return $this->category;
	}

	public function getEntry()
	{
		return $this->entry;
	}

	public function getDateEntry()
	{
		return $this->dateEntry;
	}

	//SETTERS
	public function setId(int $idHistory)
	{
		if ($idHistory > 0) {
			$this->id = $idHistory;
			return $this;
		}
	}

	public function setIdSchool(int $idSchool)
	{
		if ($idSchool > 0) {
			$this->idSchool = $idSchool;
			return $this;
		}
	}

	public function setCategory(string $category)
	{
		if (strlen($category) > 0) {
			$this->category = $category;
			return $this;
		}
	}

	public function setEntry(string $entry)
	{
		if (strlen($entry) > 0) {
			$this->entry = $entry;
			return $this;
		}
	}

	public function setDateEntry($date)
	{
		if (!empty($date)) {
			$this->dateEntry = $date;
			return $this;
		}
	}
}
