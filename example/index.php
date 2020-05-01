<?php

# Example program to demonstrate the STE...

require_once(dirname(__FILE__) . "/../steloader.php");
use \kch42\ste;

# Initialize an STECore instance
$ste = new ste\STECore(
    new ste\FilesystemStorageAccess(                # The STECore needs a StorageAccess implementation, we are using the FilesystemStorageAccess, which comes with STE.
        dirname(__FILE__) . "/templates/src",    # FilesystemStorageAccess needs a directory, where the Templates are...
        dirname(__FILE__) . "/templates/transc"  # ...and a directory for the compiled templates (write permissions needed).
    )
);

# Set STE to a more verbose behavior:
$ste->mute_runtime_errors = false;

# First, lets define some custom tags.

# <ste:uppercase> will exchange all letters with their uppercase complement
$ste->register_tag(
    "uppercase",
    function ($ste, $params, $sub) {
        $text = $sub($ste);       # Get the tags content
        return strtoupper($text); # Return the new text.
    }
);

# <ste:repeat> will repeat its content n times (<ste:for> could be used too, but i needed more examples :-P )
$ste->register_tag(
    "repeat",
    function ($ste, $params, $sub) {
        $output = "";
        if (!is_numeric($params["n"])) {
            throw new ste\RuntimeError("Sorry, but parameter n must be a number...");
        }

        for ($i = 0; $i < $params["n"]; ++$i) {
            $output .= $sub($ste);
        }

        return $output;
    }
);

# assign some data
$ste->vars["users"] = array(
    array("name" => "Foo", "username" => "foo", "online" => true),
    array("name" => "Bar", "username" => "bar", "online" => false),
    array("name" => "Baz", "username" => "baz", "online" => true)
);
$ste->vars["title"] = "cool";
$ste->vars["articles"] = array(
    array("author" => "foo", "title" => "cool article", "timestamp" => 1316553353, "excerpt" => "bla", "full" => "blablabla"),
    array("author" => "bar", "title" => "awesome",      "timestamp" => 1316552000, "excerpt" => "...", "full" => ".........."),
    array("author" => "baz", "title" => "<ingenious",   "timestamp" => 1316551000, "excerpt" => "...", "full" => ".........."),
    array("author" => "baz", "title" => "whatever...",  "timestamp" => 1316550000, "excerpt" => "...", "full" => "..........")
);

$ste->vars["foo"] = "baz";
$ste->vars["bar"] = "lol";
$ste->vars["baz"] = array("lol" => "cool");

# Execute the template and output the result
echo $ste->exectemplate("articles.html");
