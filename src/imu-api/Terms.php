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
class IMuTerms
{
	public function
	__construct($kind = 'and')
	{
		$kind = strtolower($kind);
		if ($kind != 'and' && $kind != 'or')
		{
			throw new Exception("Illegal kind: $kind");
		}
		$this->kind = $kind;
		$this->list = array();
	}

	public function
	getKind()
	{
		return($this->kind);
	}

	public function
	getList()
	{
		return($this->list);
	}

	public function
	add($name, $value, $op = null)
	{
		$term = array($name, $value, $op);
		$this->list[] = $term;
	}

	public function
	addTerms($kind)
	{
		$child = new IMuTerms($kind);
		$this->list[] = $child;
		return($child);
	}

	public function
	addAnd()
	{
		return($this->addTerms('and'));
	}

	public function
	addOr()
	{
		return($this->addTerms('or'));
	}

	public function
	toArray()
	{
		$result = array();
		$result[0] = $this->kind;

		$list = array();
		for ($i = 0; $i < count($this->list); $i++)
		{
			$term = $this->list[$i];
			if ($term instanceof IMuTerms)
			{
				$term = $term->toArray();
			}
			$list[$i] = $term;
		}
		$result[1] = $list;

		return($result);
	}

	public function
	__toString()
	{
		$result = '[';
		$result .= $this->kind;
		$result .= ', [';
		for ($i = 0; $i < count($this->list); $i++)
		{
			if ($i > 0)
			{
				$result .= ', ';
			}
			$term = $this->list[$i];
			if ($term instanceof IMuTerms)
			{
				$term = $term->__toString();
			}
			else
			{
				$term = '[' . implode(', ', $term) . ']';
			}
			$result .= $term;
		}
		$result .= ']]';
		return($result);
	}

	public function
	toString()
	{
		return($this->__toString());
	}

	private $kind = null;
	private $list = null;
}

?>
