<?php

class Sitemap
{
	private $path, $xml, $nbURL, $maxURL = 50000;

	function __construct( $path = null )
	{
		if( !is_null( $path ) )
		{
			$this -> setPath( $path );
			$this -> load();
		}

		return $this;
	}

	public function setPath( $path )
	{
		$this -> path = $path;
		return $this;
	}

	public function load()
	{
		if( !is_null( $this -> path ) )
		{
			if( file_exists( $this -> path ) )
			{
			    $this -> xml = simplexml_load_file( $this -> path );
			    $this -> nbURL = count( $this -> xml -> {'url'} );
			    $this -> checkURL();
			}
			else
			{
			    exit( 'Echec lors de l\'ouverture du sitemap' );
			}
		}
		else
		{
			exit( 'Vous devez définir le chemin du fichier avec la méthode setPath ou directement lors de l\'instanciation' );
		}

		return $this;
	}

	private function checkURL()
	{
		if( $this -> nbURL >= $this -> maxURL )
		{
			exit( 'Le nombre maximum d\'url a été atteint' );
		}

		return $this;
	}

	public function add( $loc = null, $priority = null, $changefreq = null, $lastmod = null )
	{
		$this -> checkURL();

		if( is_null( $loc ) || is_null( $priority ) )
		{
			exit( 'Vous devez au moins renseigner l\'url et la priorité');
		}

		foreach( $this -> xml -> {'url'} as $url )
		{
			if( $url -> loc == $loc )
			{
				return $this;
				break;
			}
		}

		$changefreq = is_null( $changefreq ) ? 'monthly' : $changefreq; // default = monthly
		$lastmod 	= is_null( $lastmod ) ? date( 'c' ) : $lastmod; // default = now

		$url = $this -> xml -> addChild('url');
		$url -> addChild( 'loc', $loc );
		$url -> addChild( 'lastmod', $lastmod );
		$url -> addChild( 'changefreq', $changefreq );
		$url -> addChild( 'priority', $priority );

		$this -> nbURL++;
		return $this;
	}

	public function save()
	{
		$this -> xml -> asXml( $this -> path );
		return $this;
	}
}