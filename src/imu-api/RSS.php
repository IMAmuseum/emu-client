<?php
/* KE Software Open Source Licence
** 
** Notice: Copyright (c) 2011-2013 KE SOFTWARE PTY LTD (ACN 006 213 298)
** (the "Owner"). All rights reserved.
** 
** Licence: Permission is hereby granted, free of charge, to any person
** obtaining a copy of this software and associated documentation files
** (the "Software"), to deal with the Software without restriction,
** including without limitation the rights to use, copy, modify, merge,
** publish, distribute, sublicense, and/or sell copies of the Software,
** and to permit persons to whom the Software is furnished to do so,
** subject to the following conditions.
** 
** Conditions: The Software is licensed on condition that:
** 
** (1) Redistributions of source code must retain the above Notice,
**     these Conditions and the following Limitations.
** 
** (2) Redistributions in binary form must reproduce the above Notice,
**     these Conditions and the following Limitations in the
**     documentation and/or other materials provided with the distribution.
** 
** (3) Neither the names of the Owner, nor the names of its contributors
**     may be used to endorse or promote products derived from this
**     Software without specific prior written permission.
** 
** Limitations: Any person exercising any of the permissions in the
** relevant licence will be taken to have accepted the following as
** legally binding terms severally with the Owner and any other
** copyright owners (collectively "Participants"):
** 
** TO THE EXTENT PERMITTED BY LAW, THE SOFTWARE IS PROVIDED "AS IS",
** WITHOUT ANY REPRESENTATION, WARRANTY OR CONDITION OF ANY KIND, EXPRESS
** OR IMPLIED, INCLUDING (WITHOUT LIMITATION) AS TO MERCHANTABILITY,
** FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. TO THE EXTENT
** PERMITTED BY LAW, IN NO EVENT SHALL ANY PARTICIPANT BE LIABLE FOR ANY
** CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
** TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
** SOFTWARE OR THE USE OR OTHER DEALINGS WITH THE SOFTWARE.
** 
** WHERE BY LAW A LIABILITY (ON ANY BASIS) OF ANY PARTICIPANT IN RELATION
** TO THE SOFTWARE CANNOT BE EXCLUDED, THEN TO THE EXTENT PERMITTED BY
** LAW THAT LIABILITY IS LIMITED AT THE OPTION OF THE PARTICIPANT TO THE
** REPLACEMENT, REPAIR OR RESUPPLY OF THE RELEVANT GOODS OR SERVICES
** (INCLUDING BUT NOT LIMITED TO SOFTWARE) OR THE PAYMENT OF THE COST OF SAME.
*/
require_once dirname(__FILE__) . '/IMu.php';
require_once IMu::$api . '/XML.php';

class IMuRSS
{
	public $encoding;

	/* Mandatory elements */
	public $description;
	public $link;
	public $title;

	/* Optional elements */
	public $author;
	public $category;
	public $copyright;
	public $language;

	/* iTunes-specific settings */
	public $iTunes;
	public $iTunesNS;

	/* iTunes-specific elements */
	public $explicit;
	public $image;

	public function
	__construct()
	{
		$this->encoding = 'UTF-8';
		$this->items = array();

		$this->description = '';
		$this->link = '';
		$this->title = '';

		$this->iTunes = false;
		$this->iTunesNS = 'http://www.itunes.com/dtds/podcast-1.0.dtd';
	}

	public function
	addItem()
	{
		$item = new IMuRSSItem;
		$this->items[] = $item;
		return $item;
	}

	public function
	createXML()
	{
		$xml = new IMuXMLDocument();

		$elem = $xml->element('rss');
		$elem->attr('version', '2.0');
		if ($this->iTunes)
			$elem->attr('xmlns:itunes', $this->iTunesNS);
		$elem->push();

		$elem = $xml->element('channel');
		$elem->push();

		// Mandatory
		$xml->element('description', $this->description);
		$xml->element('link', $this->link);
		$xml->element('title', $this->title);

		// Not mandatory but always included
		$date = date('r');
		$xml->element('lastBuildDate', $date);
		$xml->element('pubDate', $date);

		// Optional
		if (isset($this->author))
			$xml->element('author', $this->author);
		if (isset($this->category))
			$xml->element('category', $this->category);
		if (isset($this->copyright))
			$xml->element('copyright', $this->copyright);
		if (isset($this->language))
			$xml->element('language', $this->language);

		// iTunes-specific 
		if ($this->iTunes)
		{
			if (isset($this->explicit))
			{
				$explicit = $this->explicit;
				if (is_bool($explicit))
					$explicit = $explicit ? 'Yes' : 'No';
				$xml->element('itunes:explicit', $explicit);
			}
			if (isset($this->image))
				$xml->element('itunes:image', $this->image);
		}

		foreach ($this->items as $item)
			$item->createXML($xml);

		return $xml->saveXML();
	}

	private $items;
}

class IMuRSSItem
{
	public $author;
	public $category;
	public $description;
	public $length;
	public $link;
	public $mimeType;
	public $pubDate;
	public $title;
	public $url;

	public function
	__construct()
	{
		$this->author = '';
		$this->category = '';
		$this->description = '';
		$this->length = '';
		$this->link = '';
		$this->mimeType = '';
		$this->pubDate = '';
		$this->title = '';
		$this->url = '';
	}

	public function
	createXML($xml)
	{
		$elem = $xml->element('item');
		$elem->push();

		if (isset($this->author) && $this->author != '')
			$xml->element('author', $this->author);
		if (isset($this->category) && $this->category != '')
			$xml->element('category', $this->category);
		$xml->element('description', $this->description);

		$elem = $xml->element('enclosure');
		$elem->attr('url', $this->url);
		$elem->attr('length', $this->length);
		$elem->attr('type', $this->mimeType);

		$xml->element('guid', $this->link);
		$xml->element('link', $this->link);
		$xml->element('pubDate', $this->pubDate);
		$xml->element('title', $this->title);
	}
}
?>
