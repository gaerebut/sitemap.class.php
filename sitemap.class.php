<?php

class Sitemap
{
	private $path, $xml, $nbURL,
			$xmlVersion = '1.0',
			$preserveWhiteSpace = false,
			$formatOutput = true,
			$maxURL = 50000;

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

	public function setVersion( $version )
	{
		$this -> xmlVersion = $version;
		return $this;
	}

	public function setPreserveWhiteSpace( $isPreserve = false )
	{
		$this -> preserveWhiteSpace = $isPreserve;
		return $this;
	}

	public function setFormatOuput( $isOuput = true )
	{
		$this -> formatOutput = $isOuput;
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
			    exit( "Echec lors de l'ouverture du sitemap" );
			}
		}
		else
		{
			exit( "Vous devez définir le chemin du fichier avec la méthode setPath ou directement lors de l'instanciation" );
		}

		return $this;
	}

	private function checkURL()
	{
		if( $this -> nbURL >= $this -> maxURL )
		{
			exit( "Le nombre maximum d'URL a été atteint" );
		}

		return $this;
	}

	public function add( $loc = null, $priority = null, $changefreq = null, $lastmod = null )
	{
		$this -> checkURL();

		if( is_null( $loc ) || is_null( $priority ) )
		{
			exit( "d" );
		}

		foreach( $this -> xml -> {'url'} as $url ) // no duplicates
		{
			if( $url -> loc == $loc )
			{
				return $this;
				break;
			}
		}

		$changefreq = is_null( $changefreq ) ? 'monthly' : $changefreq; // default = monthly
		$lastmod 	= is_null( $lastmod ) ? date( 'c' ) : $lastmod; 	// default = now

		$url = $this -> xml -> addChild('url');
		$url -> addChild( 'loc', 		$loc );
		$url -> addChild( 'lastmod', 	$lastmod );
		$url -> addChild( 'changefreq', $changefreq );
		$url -> addChild( 'priority', 	$priority );

		$this -> nbURL++;
		return $this;
	}

	public function edit( $loc_old = null, $loc = null, $priority = null, $changefreq = null, $lastmod = null )
	{
		$this -> checkURL();

		if( is_null( $loc_old ) || is_null( $loc ) || is_null( $priority ) )
		{
			exit( "Vous devez au moins renseigner l'URL de remplacement, la nouvelle URL et la priorité" );
		}

		foreach( $this -> xml -> {'url'} as $url ) // search old loc
		{
			if( $url -> loc == $loc_old )
			{
				$changefreq = is_null( $changefreq ) ? 'monthly' : $changefreq; // default = monthly
				$lastmod 	= is_null( $lastmod ) ? date( 'c' ) : $lastmod; 	// default = now

				$url -> loc 		= $loc;
				$url -> lastmod 	= $lastmod;
				$url -> changefreq 	= $changefreq;
				$url -> priority 	= $priority;
			}
		}

		return $this;
	}

	public function remove( $loc = null )
	{
		if( is_null( $loc ) )
		{
			exit( "Vous devez renseigner l'URL à supprimer" );
		}

		$index = 0;
		foreach( $this -> xml -> url as $url ) // search old loc
		{
			if( $url -> loc == $loc )
			{
				break;
			}

			$index++;
		}

		unset( $this -> xml -> url[ $index ] );

		return $this;
	}

	public function save()
	{
		$dom = dom_import_simplexml( $this -> xml ) -> ownerDocument; // Normally keep formatting
		$dom -> preserveWhiteSpace 	= $this -> preserveWhiteSpace; //default = false;
		$dom -> formatOutput 		= $this -> formatOutput; //defaut = true;
		$dom -> save( $this -> path );
		
		return $this;
	}
}