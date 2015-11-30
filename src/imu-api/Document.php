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
require_once IMu::$api . '/Exception.php';
require_once IMu::$api . '/Trace.php';

class IMuDocument
{
	/* Ideally IMuDocument would be a subclass of DOMDocument (as it was
	** in earlier IMu versions).
	**
	** However, if a DOM document is constructed using new DOMDocument() [or
	** by constructing a subclass] the document cannot be given a DTD.
	**
	** Creating DOM documents with DTDs is required by the IMu web framework.
	** This means that the only effective way to construct the document is to
	** use DOMImplementation::createDocument().
	**
	** However, in PHP this mechanism cannot construct objects which are
	** subclasses of DOMDocument, only DOMDocument objects themselves.
	** (See http://bugs.php.net/bug.php?id=53286 for more information)
	**
	** Because of this IMuDocument is implemented as a wrapper around
	** DOMDocument rather than a subclass.
	*/
	public function
	__construct($name = null, $public = null, $system = null)
	{
		/* DOMImplementation methods seem to be very fussy about how
		** they are passed arguments. We do it this way to reduce errors and
		** warnings that we don't need.
		*/
		$impl = new DOMImplementation();
		if (is_null($name))
			$this->_dom = $impl->createDocument('', '');
		else
		{
			$dtd = $impl->createDocumentType($name, $public, $system);
			$this->_dom = $impl->createDocument('', '', $dtd);
		}

		$this->elementClass();

		$this->_stack = array($this->_dom);
	}

	/* Node construction */
	public function
	comment($data)
	{
		return $this->newComment($data, $this->_stack[0]);
	}

	public function
	element($name)
	{
		return $this->newElement($name, $this->_stack[0]);
	}

	public function
	text($content)
	{
		return $this->newTextNode($content, $this->_stack[0]);
	}

	public function
	fragment($xml)
	{
		return $this->newDocumentFragment($xml, $this->_stack[0]);
	}

	public function
	push($node)
	{
		array_unshift($this->_stack, $node);
	}

	public function
	pop($node = null)
	{
		$count = count($this->_stack);
		if ($node === null)
			$count--;
		else
		{
			for ($i = 0; $i < $count; $i++)
			{
				if ($this->_stack[$i] === $node)
				{
					$count -= $i + 1;
					break;
				}
			}
		}
		while (count($this->_stack) > $count)
			array_shift($this->_stack);
	}

	public function
	top()
	{
		return $this->_stack[0];
	}

	/* Node constructors */
	public function
	elementClass($class = 'IMuDocumentElement')
	{
		/* Prior to PHP version 5.2.2, a previously registered class had
		** to be unregistered before being able to register a new class
		** extending the same base class.
		**
		** See http://www.php.net/manual/en/domdocument.registernodeclass.php
		*/
		$this->_dom->registerNodeClass('DOMElement', null);

		$this->_dom->registerNodeClass('DOMElement', $class);
	}

	public function
	newComment($data, $parent)
	{
		$node = $this->_dom->createComment($data);
		$parent->appendChild($node);
		return $node;
	}

	public function
	newElement($name, $parent)
	{
		$node = $this->createElement($name);
		$parent->appendChild($node);
		return $node;
	}

	public function
	newTextNode($content, $parent)
	{
		$node = $this->_dom->createTextNode($content);
		$parent->appendChild($node);
		return $node;
	}

	public function
	newDocumentFragment($xml, $parent)
	{
		$node = $this->_dom->createDocumentFragment();
		$node->appendXML($xml);
		$parent->appendChild($node);
	}

	/* DOM wrapper */
	public function
	getDOM()
	{
		return $this->_dom;
	}

	/* properties */
	public function
	__get($name)
	{
		return $this->_dom->$name;
	}

	public function
	__set($name, $value)
	{
		$this->_dom->$name = $value;
	}

	/* methods */
	public function
	createElement($name, $value = '')
	{
		$node = $this->_dom->createElement($name, $value);
		$node->document = $this;
		return $node;
	}

	public function
	__call($name, $args)
	{
		return call_user_func_array(array($this->_dom, $name), $args);
	}

	protected $_dom;
	protected $_stack;
}

class IMuDocumentElement extends DOMElement
{
	public $document;

	public function
	attr($name, $value = null)
	{
		if ($value !== null)
			$this->setAttribute($name, $value);
		return $this->getAttribute($name);
	}

	public function
	comment($data)
	{
		return $this->document->newComment($data, $this);
	}

	public function
	element($name)
	{
		return $this->document->newElement($name, $this);
	}

	public function
	text($content)
	{
		return $this->document->newTextNode($content, $this);
	}

	public function
	fragment($xml)
	{
		return $this->document->newDocumentFragment($xml, $this);
	}

	public function
	push()
	{
		$this->document->push($this);
	}

	public function
	pop()
	{
		$this->document->pop($this);
	}
}
?>
