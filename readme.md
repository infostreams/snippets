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


##Supported syntax##

###Links###
By default, it supports links to external sites and to pages on your own site:

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

Specify a text for the link

    (file: products/brochure.pdf text:our brochure)

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
First, define your class, and make sure it implements the ```\Phile\Plugin\Infostreams\Snippets\Snippets```
interface. Then, make sure that it is included by PhileCMS.

    class MySnippets implements \Phile\Plugin\Infostreams\Snippets\Snippets {
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
    }

Now, you can add the 'skype', 'facebook' and 'scroller' Snippets (and any future ones you define)
by adding the following code to PhileCMS's configuration file:

    $config['plugins']['infostreams\\snippets']['snippets'] = array(
        new MySnippets(),
        'some_other_snippet' => function($a, $b) { },
        'yet_another_snippet' => array(new MyOtherClass(), 'method')
    );

###Kirbytext###
This plugin is an open source (MIT licensed) re-implementation of Kirbytext.

It correctly parses *most* of the syntax of [Kirbytext](http://getkirby.com/docs/content/text),
even though the resulting HTML might differ. There is no guarantee of compatibility between the
two, although a high level of interoperability is one of the design goals.

