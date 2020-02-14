<?php

namespace Chemin\ArtSchool\Model;

class School
{
	private $id,
			$idAdmin,
			$name,
			$nameAdmin,
			$code,
			$nbEleve,
			$nbActiveAccount,
			$dateInscription,
			$dateDeadline,
			$logo,
			$isActive;

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

	public function getIdAdmin()
	{
		return $this->idAdmin;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getNameAdmin()
	{
		return $this->nameAdmin;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function getNbEleve()
	{
		return $this->nbEleve;
	}

	public function getNbActiveAccount()
	{
		return $this->nbActiveAccount;
	}

	public function getDateInscription()
	{
		return $this->dateInscription;
	}

	public function getDateDeadline()
	{
		return $this->dateDeadline;
	}

	public function getLogo()
	{
		return $this->logo;
	}

	public function getIsActive()
	{
		return $this->isActive;
	}

	//SETTERS
	public function setId(int $idSchool)
	{
		if ($idSchool > 0) {
			$this->id = $idSchool;
			return $this;
		}
	}

	public function setIdAdmin(int $idAdmin)
	{
		if ($idAdmin > 0) {
			$this->idAdmin = $idAdmin;
			return $this;
		}
	}

	public function setName(string $name)
	{
		if (strlen($name) > 0) {
			$this->name = $name;
			return $this;
		}
	}

	public function setNameAdmin(string $nameAdmin)
	{
		if (strlen($nameAdmin) > 0) {
			$this->nameAdmin = $nameAdmin;
			return $this;
		}
	}

	public function setCode(string $code)
	{
		if (strlen($code) > 0) {
			$this->code = $code;
			return $this;
		}
	}

	public function setNbEleve(int $nbEleve)
	{
		if ($nbEleve > 0) {
			$this->nbEleve = $nbEleve;
			return $this;
		}
	}

	public function setNbActiveAccount(int $nbActiveAccount)
	{
		if ($nbActiveAccount >= 0) {
			$this->nbActiveAccount = $nbActiveAccount;
			return $this;
		}
	}

	public function setDateInscription($date)
	{
		if (!empty($date)) {
			$this->dateInscription = $date;
			return $this;
		}
	}

	public function setDateDeadline($date)
	{
		if (!empty($date)) {
			$this->dateDeadline = $date;
			return $this;
		}
	}

	public function setLogo(string $logoPath)
	{
		if (strlen($logoPath) > 0) {
			$this->logo = $logoPath;
			return $this;
		}
	}

	public function setIsActive(bool $isActive)
	{
		$this->isActive = $isActive;
		return $this;
	}
}
