<?php

namespace Chemin\ArtSchool\Model;

class Post
{
	private $id,
			$idAuthor,
			$imgPath,
			$description,
			$datePublication,
			$comments;

	public function __construct(array $data = null)
	{
		if (!empty($data)) {
			$this->hydrate($data);
		}
	}

	public function hydrate(array $data)
	{
		foreach ($data as $key => $value) {
			$method = 'set' . ucfirst($key);

			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	//GETTERS
	public function getId()
	{
		return $this->id;
	}

	public function getIdAuthor()
	{
		return $this->idAuthor;
	}

	public function getImgPath()
	{
		return $this->imgPath;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function getDatePublication()
	{
		return $this->datePublication;
	}

	public function getComments()
	{
		return $this->comments;
	}

	//SETTERS
	public function setId(int $id)
	{
		if ($id > 0){
			$this->id = $id;
			return $this;
		}
	}

	public function setIdAuthor(int $idAuthor)
	{
		if ($idAuthor > 0){
			$this->idAuthor = $idAuthor;
			return $this;
		}
	}

	public function setImgPath(string $imgPath)
	{
		if (strlen($imgPath) > 0){
			$this->imgPath = $imgPath;
			return $this;
		}
	}

	public function setDescription(string $description)
	{
		if (strlen($description) > 0){
			$this->description = $description;
			return $this;
		}
	}

	public function setDatePublication($date)
	{
		if (!empty($date)){
			$this->datePublication = $date;
			return $this;
		}
	}

	public function setComments(array $comments)
	{
		$this->comments = $comments;
		return $this;
	}
}
