<?php

namespace WSL;

class ImageSearch
{
    private $flickrApiKey;

    public function __construct($flickrApiKey)
    {
        $this->flickrApiKey = $flickrApiKey;
    }

    public function getAllImages($query = null)
    {
    	$query = trim($_REQUEST['content']);
    	$tags = explode(',', $query);
    	$imageUrls = array();

    	foreach ($tags as $tag) {
    	    $imageUrls = array_merge($imageUrls, $this->getFlickrImages($tag));
    	    $imageUrls = array_merge($imageUrls, $this->getWikiMediaImages($tag));
    	}
    	print_r(json_encode($imageUrls));
    }

    public function getFlickrImages($query = null)
    {
	$search = 'http://flickr.com/services/rest/?method=flickr.photos.search&api_key=' . $this->flickrApiKey . '&tags=' . urlencode($query) . '&text=' . urlencode($query) . '&extras=url_m&safe_search=1&per_page=10&format=php_serial&sort=relevance&license=6,4';
	$result = file_get_contents($search);
	$result = unserialize($result);
	$photos = $result['photos']['photo'];

        $urls = array();
        foreach($photos as $photo) {
	    $urls[] = $photo['url_m'];
        }
        return $urls;
    }

    public function getWikiMediaImages($query = null)
    {
	$search = 'http://commons.wikimedia.org/w/api.php?action=query&list=allimages&aiprop=url%7Cmime&format=php&redirects&aifrom='. urlencode($query) . '&ailimit=10';
        $result = file_get_contents($search);
        $results = unserialize($result);
        $photos = $results['query']['allimages'];

        $urls = array();
        foreach($photos as $photo) {
	    $urls[] = $photo['url'];
        }
        return $urls;
    }
}

// $flickr = new Flickr();
// $flickr->getAllImages();
