Exterminator
============

**A simple debugging composer package for Laravel 4 that allows you to output color-coded variable dumps on the screen depending on whether a 'debug' cookie is present.**

Variable dumps are color-coded and just as descriptive as PHP's `var_dump()` method. You may hide the Exterminator window by clicking the "Hide" button and may single-click on any variable to select it in its entirety. These things help to make Exterminator much nicer to work with than just a crude `var_dump`.

Using the `Dbg::display()` method, you can dump all of your variables at the end of your view to prevent any data output from breaking your rendered HTML page. They variable output is also contained in HTML markup and use various Javascript methods for enhanced use as described above.

- [Installation](#installation)
- [Enabling Exterminator](#enabling)
- [Usage](#usage)

<a name="installation"></a>
## Installation

To install Exterminator, make sure "regulus/exterminator" has been added to Laravel 4's `composer.json` file.

	"require": {
		"regulus/exterminator": "dev-master"
	},

Then run `php composer.phar update` from the command line. Composer will install the Exterminator package. Now, all you have to do is register the service provider and set up Exterminator's alias in `app/config/app.php`. Add this to the `providers` array:

	'Regulus\Exterminator\ExterminatorServiceProvider',

And add this to the `aliases` array:

	'Dbg' => 'Regulus\Exterminator\Exterminator',

You may use 'Exterminator', 'Debug', or another alias, but 'Dbg' is recommended for the sake of simplicity. Exterminator is now ready to go.

<a name="enabling"></a>
## Enabling Exterminator

To enable Exterminator, navigate to `http://site.com/debug/debug1913`. This will enable the viewing of Exterminator's data output by placing a cookie on your machine. Note that the `debug1913` part is your Exterminator access code and can be configured in `config.php`.

<a name="usage"></a>
## Usage

**Basic usage:**

To display a variable after you have set your `debug` cookie:

	$var = array(
		'boolean' => true,
		'number'  => 3.43,
		'string'  => 'Testing Exterminator',
		'array'   => array(
			'boolean' => false,
			'number'  => 5,
			'object'  => (object) array(
				'Number One',
				2,
				3.0,
			),
		),
	);
	Dbg::display($var);

**Displaying multiple variables:**

To display multiple variables, use Exterminator's "add" method, `Dbg::a()`:

	Dbg::a($var);
	Dbg::a($var2);

Then at in the footer of your website add a simple `Dbg::display()` with no arguments:

			<?php Dbg::display(); ?>
		</body>
	</html>

**Adding variable names to dumped variables:**

You can get the variable names to show up in the upper-right area of an outputted variable using PHP's `get_defined_vars()`. Please note that this may not work in all cases. Here is how to do it:

	$definedVars = get_defined_vars();

	Dbg::a($var, $definedVars);
	Dbg::a($var2, $definedVars);

You can also do it manually if the "add" method's second argument is a string:

	Dbg::a($var, 'var');