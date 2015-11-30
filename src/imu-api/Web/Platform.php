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
require_once dirname(__FILE__) . '/../IMu.php';

class IMuWebPlatform
{
	public function
	__construct()
	{
		/* Determine client platform
		**
		** We could use PHP's get_browser() but this requires installing
		** a browscap.ini file and editing php.ini to point to it.
		** Unfortunately this cannot be done with ini_set().
		** 
		** Because our requirements are pretty rudimentary we roll our
		** own.
		*/
		$this->agent = $_SERVER['HTTP_USER_AGENT'];
		$this->profile = '';
		if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
			$this->profile = $_SERVER['HTTP_X_WAP_PROFILE'];

		$a = strtolower($this->agent);
		$p = strtolower($this->profile);

		$this->device = new stdClass;
		$this->device->name = 'unknown';
		$this->device->type = 'unknown';
		$this->device->is = new stdClass;
		$this->device->is->android = false;
		$this->device->is->desktop = false;
		$this->device->is->ipad = false;
		$this->device->is->iphone = false;
		$this->device->is->linux = false;
		$this->device->is->mac = false;
		$this->device->is->mobile = false;
		$this->device->is->pc = false;
		$this->device->is->phone = false;
		$this->device->is->tablet = false;

		$this->os = new stdClass;
		$this->os->name = 'unknown';
		$this->os->version = 'unknown';
		$this->os->is = new stdClass;
		$this->os->is->android = false;
		$this->os->is->ios = false;
		$this->os->is->mac = false;
		$this->os->is->other = true;
		$this->os->is->unix = false;
		$this->os->is->windows = false;

		$this->browser = new stdClass;
		$this->browser->name = 'unknown';
		$this->browser->version = 'unknown';
		$this->browser->is = new stdClass;
		$this->browser->is->android = false;
		$this->browser->is->chrome = false;
		$this->browser->is->firefox = false;
		$this->browser->is->ie = false;
		$this->browser->is->konqueror = false;
		$this->browser->is->opera = false;
		$this->browser->is->safari = false;

		/* device & os
		*/
		if (preg_match('/windows nt (\d+\.\d+)/', $a, $m))
		{
			$this->device->name = 'desktop';
			$this->device->is->pc = true;

			$this->device->type = 'desktop';
			$this->device->is->desktop = true;

			$this->os->name = 'windows';
			if ($m[1] == '6.1')
				$this->os->version = '7';
			else if ($m[1] == '6.0')
				$this->os->version = 'vista';
			else if ($m[1] == '5.2')
				$this->os->version = 'server-2003';
			else if ($m[1] == '5.1')
				$this->os->version = 'xp';
			$this->os->is->windows = true;
		}
		else if (preg_match('/\(ipad;.*\bos (\d+(_\d+)*)/', $a, $m))
		{
			$this->device->name = 'ipad';
			$this->device->is->ipad = true;

			$this->device->type = 'tablet';
			$this->device->is->mobile = true;
			$this->device->is->tablet = true;

			$this->os->name = 'ios';
			$this->os->version = $m[1];
			$this->os->is->ios = true;
		}
		else if (preg_match('/\(iphone;.*\bos (\d+(_\d+)*)/', $a, $m))
		{
			$this->device->name = 'iphone';
			$this->device->is->iphone = true;

			$this->device->type = 'phone';
			$this->device_mobile = true;
			$this->device_phone = true;

			$this->os->name = 'ios';
			$this->os->version = $m[1];
			$this->os->is->ios = true;
		}
		else if (preg_match('/\bandroid(\s+(\d+(\.\d+)*))?/', $a, $m))
		{
			$this->device->name = 'android';
			$this->device->is->android = true;

			$this->device->type = 'mobile';
			$this->device->is->mobile = true;

			$this->os->name = 'android';
			if (isset($m[2]))
				$this->os->version = $m[2];
			$this->os->is->android = true;

			/* It's very hard to separate android phones from tablets
			**
			** There is scope to use User Agent Profiles (passed in
			** HTTP_X_WAP_PROFILE) but we don't need it (yet).
			**
			** We know about a few so we hard-code checks for them here.
			*/

			// HTC
			if (preg_match('/\bhtc\b/', $a))
			{
				$this->device->name = 'htc-phone';

				$this->device->type = 'phone';
				$this->device->is->phone = true;
			}

			// Samsung
			else if (preg_match('/\bgt-i9300\b/', $a))
			{
				$this->device->name = 'samsung-phone';

				$this->device->type = 'phone';
				$this->device->is->phone = true;
			}
			else if (preg_match('/\bgt-p7500\b/', $a))
			{
				$this->device->name = 'samsung-tablet';

				$this->device->type = 'tablet';
				$this->device->is->tablet = true;
			}
		}
		else if (preg_match('/\(macintosh;.*\bos [a-z]* (\d+(_\d+)*)/', $a, $m))
		{
			$this->device->name = 'desktop';
			$this->device->is->mac = true;

			$this->device->type = 'desktop';
			$this->device->is->desktop = true;

			$this->os->name = 'mac';
			$this->os->version = $m[1];
			$this->os->is->mac = true;
		}
		else if (preg_match('/\blinux\b/', $a, $m))
		{
			$this->device->name = 'linux';
			$this->device->is->linux = true;

			$this->device->type = 'desktop';
			$this->device->is->desktop = true;

			$this->os->name = 'unix';
			$this->os->is->unix = true;
		}

		/* browser
		*/
		if (preg_match('/msie[\/ ](\\d+(\\.\\d+)*)/', $a, $m))
		{
			$this->browser->name = 'ie';
			$this->browser->version = $m[1];
			$this->browser->is->ie = true;
		}
		else if (preg_match('/(chrome|crios)[\/ ](\\d+(\\.\\d+)*)/', $a, $m))
		{
			$this->browser->name = 'chrome';
			$this->browser->version = $m[2];
			$this->browser->is->chrome = true;
		}
		else if (preg_match('/firefox[\/ ](\\d+(\\.\\d+)*)/', $a, $m))
		{
			$this->browser->name = 'firefox';
			$this->browser->version = $m[1];
			$this->browser->is->firefox = true;
		}
		else if (preg_match('/opera[\/ ](\\d+(\\.\\d+)*)/', $a, $m))
		{
			$this->browser->name = 'opera';
			$this->browser->version = $m[1];
			$this->browser->is->opera = true;
		}
		else if (preg_match('/konqueror[\/ ](\\d+(\\.\\d+)*)/', $a, $m))
		{
			$this->browser->name = 'konqueror';
			$this->browser->version = $m[1];
			$this->browser->is->konqueror = true;
		}
		else if (preg_match('/android[\/ ](\\d+(\\.\\d+)*)/', $a, $m))
		{
			$this->browser->name = 'android';
			$this->browser->version = $m[1];
			$this->browser->is->android = true;
		}
		else if (preg_match('/safari[\/ ](\\d+(\\.\\d+)*)/', $a, $m))
		{
			$this->browser->name = 'safari';
			$this->browser->version = $m[1];
			$this->browser->is->safari = true;
		}
		
		/* attributes
		*/
		$this->attributes = array();
		foreach (get_object_vars($this->device->is) as $name => $value)
			if ($value)
				$this->attributes[$name] = true;
		foreach (get_object_vars($this->os->is) as $name => $value)
			if ($value)
				$this->attributes[$name] = true;
		foreach (get_object_vars($this->browser->is) as $name => $value)
			if ($value)
				$this->attributes[$name] = true;
	}

	public $agent;
	public $profile;

	public $device;
	public $os;
	public $browser;
	public $attributes;
}
?>
