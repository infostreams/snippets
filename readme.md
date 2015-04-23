#Snippets#

A [PhileCMS](https://github.com/PhileCMS/Phile) plugin that extends the Markdown syntax to
easily add links, images, videos or other content to your page. These 'snippets' are easy to
add and customize, and the plugin comes with a large selection of useful snippets out of the box.

This plugin works with the default Markdown parser, but it should also work with any of the other
plugins that offer Markdown alternatives. It should even work if applied to regular HTML files.

##Installation##

###With composer###

    php composer.phar require infostreams/snippets:*

###Download###

* Install [Phile](https://github.com/PhileCMS/Phile)
* Clone this repo into plugins/infostreams/snippets


###Activation###

After you have installed the plugin. You need to add the following line to your config.php file:

    $config['plugins']['infostreams\\snippets'] = array('active' => true);


##What it does##

With this plugin you can include pre-defined HTML snippets in your Markdown. This means you can
write things like:

    This is some text before I introduce you to the main point of this example, the YouTube video
    that I want to include here:

    (youtube: https://www.youtube.com/watch?v=mSB71jNq-yQ)

    In case you can't see that video, you can have a look at the slideshow that I'll include for
    your pleasure: (slideshow: [images/a.jpg, images/b.jpg, images/c.jpg] heading: Awesome slideshow)

    I've taken these images from (link: site.com text: this site)

Basically it extends standard Markdown syntax to make it easier to write interactive web pages
with more than just basic formatting. Markdown is great for adding headings, bold text, links and
lists to your page, but it has almost no provisions for adding other elements to your page. With
Snippets you can add these elements easily. You can use one of the many included snippets, or
you can easily define new ones yourself.


##Supported syntax##

**General note**: snippet tags can be specified over multiple lines, and attribute values can
be scalar values (strings or integers) or lists (arrays) of values - in which case they will be
parsed as such.

###Links###
You can link to external sites and to pages on your own site:

    (link: cnn.com)
    (link: products/mastergrill5000)

Specify the link text as follows:

    (link: cnn.com text: the site I was talking about)

Add a title that displays when you move your mouse over the link:

    (link: cnn.com title: click to visit!)

The link opens in a popup by adding the 'popup' attribute:

    (link: cnn.com popup: true)

You can specify a custom CSS class for styling purposes:

    (link: cnn.com class: my-css-class)

It is possible to combine these attributes, or to leave attributes out:

    (link: cnn.com class: my-css-class popup:true text:CNN title:Click to go to the CNN homepage)

###Email addresses###
You can link to email addresses like this:

    (email: someone@example.com)

Provide a text for the link:

    (email: someone@example.com text:My email address)

Add a title that displays when you move your mouse over the link:

    (email: someone@example.com title:Click to email me)

You can specify a custom CSS class for styling purposes:

    (email: someone@example.com class: my-css-class)

###Images###
You can include images from your own site, or from external sites:

    (image: products/mastergrill5000/grilling.jpg)
    (image: https://raw.githubusercontent.com/dcurtis/markdown-mark/master/png/66x40-solid.png)

Set the width and height:

    (image: products/mastergrill5000/grilling.jpg width:400)
    (image: products/mastergrill5000/grilling.jpg width:400 height:600)

Specify alt text:

    (image: products/mastergrill5000/grilling.jpg alt:Smoking!)

Add a caption:

    (image: products/mastergrill5000/grilling.jpg caption:Grilling on the MasterGrill5000)

Specify a CSS class:

    (image: products/mastergrill5000/grilling.jpg class:highlight)

Link the image to a page or an external site:

    (image: products/mastergrill5000/grilling.jpg link:products/mastergrill5000)
    (image: products/mastergrill5000/amazon.jpg link:amazon.com/buy-the-mg5000)

Add a 'srcset' attribute to deal with responsive images:

    (image: products/mastergrill5000/grilling.jpg srcset:products/mastergrill5000/grilling@2x.jpg 2x)

###Files###

Include files from your own site or from external sites:

    (file: products/brochure.pdf)
    (file: https://bitcoin.org/bitcoin.pdf)

Specify a text for the link:

    (file: products/brochure.pdf text:our brochure)

Force download of the file (limited browser support unfortunately):

    (file: products/brochure.pdf download:true)

Specify a CSS class:

    (file: products/brochure.pdf class:highlight)

###YouTube videos###

Embed a YouTube video in your document:

    (youtube: https://www.youtube.com/watch?v=mSB71jNq-yQ)

Specify width and/or height:

    (youtube: https://www.youtube.com/watch?v=mSB71jNq-yQ width:640)
    (youtube: https://www.youtube.com/watch?v=mSB71jNq-yQ width:640 height:480)

###Vimeo videos###

Embed a Vimeo video in your document:

    (vimeo: http://vimeo.com/63968108)

Specify width and/or height:

    (vimeo: http://vimeo.com/63968108 width:700)
    (vimeo: http://vimeo.com/63968108 width:700 height:393)

Options to include portrait, title and byline:

    (vimeo: http://vimeo.com/63968108 portrait:false)
    (vimeo: http://vimeo.com/63968108 portrait:false byline:false)
    (vimeo: http://vimeo.com/63968108 portrait:false byline:false title:true)

###Twitter###

Embed a link to a Twitter profile:

    (twitter: @nytimes)

Embed a link to a Twitter hashtag:

    (twitter: #superimportant)

Embed a link to a Twitter search:

    (twitter: #superimportant keywords)

Specify text for the link:

    (twitter: @nytimes text:The New York Times on Twitter)

Specify a CSS class:

    (twitter: @nytimes class:twitter-button)

###Gist###

Embed a Github Gist:

    (gist: https://gist.github.com/1)

Display a specific file in that gist:

    (gist: https://gist.github.com/1 file: gistfile1.txt)

###Telephone numbers###
Add a link to a telephone number:

    (tel: 555-0100)

Specify which text to use for the link:

    (tel: 555-0100 text:Call me!)

You can specify a custom CSS class for styling purposes:

    (tel: 555-0100 class:phone)


#Adding your own snippets#

It is very easy to define your own snippets. Most likely, you will want to do this through your
PhileCMS configuration file:

##Simple case##
Example:

    $config['plugins']['infostreams\\snippets']['snippets'] = array(
        'skype' => function($name, $action="call", $text=null) {
                       if (is_null($text)) {
                         $text = "Contact '$name' on Skype";
                       }
                       return "<a href='skype:$name?$action'>$text</a>";
                   }
    );

This adds a 'skype' snippet that you can use (in your markdown) as follows:

    (skype: myfriend)

This would be rendered as:

    <a href='skype:myfriend?call'>Contact 'myfriend' on Skype</a>

The PHP function has two more parameters, which means that the 'skype' snippets allows for
two more attributes to be specified, as in:

    (skype: my.account.on.skype action:chat text:Chat to me on Skype!)

This would produce the following HTML:

    <a href='skype:my.account.on.skype?chat'>Chat to me on Skype!</a>

##Using existing functions##

First, define your snippet in a class somewhere, and make sure it gets included:

    class MySnippets {
        public function skype($name, $action="call", $text=null) {
            if (is_null($text)) {
                $text = "Contact '$name' on Skype";
            }
            return "<a href='skype:$name?$action'>$text</a>";
        }
    }

Then, add the 'skype' snippet to the Snippets config:

    $config['plugins']['infostreams\\snippets']['snippets'] = array(
        'skype' => array(new MySnippets(), 'skype'),
    );

##Using a Snippets class##

It is also possible to define a class with multiple Snippets and add them all at once.
First, define your class, and make sure it extends the ```\Phile\Plugin\Infostreams\Snippets\Snippets```
class (which gives you access to some common functionality). Then, make sure that it is included by 
PhileCMS. There's a step-by-step guide [here](https://github.com/infostreams/snippets/issues/2#issuecomment-95512554) 
that explains how to do that properly.

    class MySnippets extends \Phile\Plugin\Infostreams\Snippets\Snippets {
        public function skype($name, $action="call", $text=null) {
            if (is_null($text)) {
                $text = "Contact '$name' on Skype";
            }
            return "<a href='skype:$name?$action'>$text</a>";
        }

        public function facebook($who, $attribute, $other_attribute) {
            // ...
        }

        public function scroller($pictures) {
            // ...
        }

        public function youtube_popup($link) {
            // ...
        }
    }

Now, you can add the `skype`, `facebook`, `scroller` and `youtube_popup` snippets (and any future
ones you define) by adding the following code to PhileCMS's configuration file:

    $config['plugins']['infostreams\\snippets']['snippets'] = array(
        new MySnippets(),
        'some_other_snippet' => function($a, $b) { },
        'yet_another_snippet' => array(new MyOtherClass(), 'method')
    );

For convenience and aesthetic reasons, snippets that have an underscore in their name (such as
`youtube_popup`) can also be used with a dash instead. So instead of writing `(youtube_popup: ....)`
you can also write `(youtube-popup: ....)`.

#Attribute values: arrays and JSON#

Parameter values don't need to be just strings or numbers. They can be arrays too! You can even
provide JSON strings - they will be parsed and converted into something that you can use inside
your snippet. For example, the following code defines a 'slideshow' snippet:

    $config['plugins']['infostreams\\snippets']['snippets'] = array(
        'slideshow' => function($images, $header="") {
                       $html = "";
                       if (!is_null($text)) {
                         $html .= "<h1>" . $header . "</h1>";
                       }
                       if (is_array($images)) {
                         $html .= "\n<ul>";
                         foreach ($images as $key=>$value) {
                           if (is_numeric($key)) {
                             $filename = $value;
                             $description = "";
                           } else {
                             $filename = $key;
                             $description = "<span class='description'>" . $value . "</span>";
                           }
                           $html .= "\n<li><img src='$filename' />$description</li>";
                         }
                         $html .= "\n<ul>";
                       }
                       return "<div class='slideshow'>" . $html . "</div>";
                   }
    );

This code generates the HTML for a simple slideshow. It can be used as follows:

    (slideshow: [content/images/a.jpg, content/images/b.jpg, content/images/c.jpg] heading: It's a slideshow!)

Here you see how you can provide multiple values for one field. The above example would create a
slideshow for the images a.jpg, b.jpg and c.jpg.

However, the snippet can also be used to label each image in the slideshow with a description. That
can be done as follows. For readability, the snippet is wrapped over more than one line:

    (slideshow: [
        content/images/a.jpg: This is my description,
        content/images/b.jpg: "This one description, it contains a comma -- so we wrap it in quotes"
        content/images/c.jpg: However\, if you don't want to quote\, you don't need to
                              - it could get ugly quickly though
        ]
        heading: It's a slideshow!)

The list of images is now a PHP-like array of (key, value) pairs. The 'key' is the filename of the
image, the 'value' is the description of the image. This way, you can write snippets that only work
if the author gives it more than one value - such as is the case in a slideshow. It is possible to
build elaborate data structures using this syntax.

##Escaping text##
You can observe that in most cases you don't need to surround the description by quotation marks. You
can if you want to, though -- as is shown in the second description (the one for b.jpg).

As long as the text doesn't contain a comma, colon, or one of '[', '{', '}' or ']', then you don't
need quotation marks. If it *does* contain one of those, you can 'escape' it by placing a '\\' before
the offending character, as is shown in the third description (the one for c.jpg).

##JSON##
Finally, if you don't like the above syntax, you can also just use plain old JSON:

    (slideshow:  {
         "content\/images\/a.jpg":"This is my description",
         "content\/images\/b.jpg":"This one description, it contains a comma -- so we wrap it in quotes",
         "content\/images\/c.jpg":"However, if you don't want to quote, you don't need to - it could get ugly quickly though"
        }
        heading: It's a slideshow!)


###Kirbytext###
This plugin is an open source (MIT licensed) re-implementation and extension of [Kirbytext](http://getkirby.com/docs/content/text).

It correctly parses *most* of Kirbytext's syntax, even though the resulting HTML might differ.
There is no guarantee of compatibility between the two, although a high level of backwards
interoperability is one of the design goals.