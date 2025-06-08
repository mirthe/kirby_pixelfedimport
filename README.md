# Kirby Plugin: Pixelfed import

This plugin allows you to import posts from Pixelfed to your Kirby site.

## Git submodule

```
git submodule add https://github.com/mirthe/kirby_pixelfedimport site/plugins/pixelfed-import
```

## Usage

In your Pixelfed settings, go to Applications and create a Personal Access Token. Add this to your Kirby config where XX is your token. 
    
    'pixelfed.token' => 'XXX'

Run the routine on

    https://yoursite.com/pixelfed/getaccount

This will show info on your account. Here you will also find your account ID. Add this number to your Kirby config:

    'pixelfed.token' => 'XXX',
    'pixelfed.clientid' => 'YYY'

Then you are ready to retrieve your posts, run the routine on

    https://yoursite.com/pixelfed/getlatest

You can add optional parameters for a limit (Retrieve this many results or fewer) and a since_id (Retrieve results newer than this identifier). The URL per post contains the identifier and is stored in the markdown file, in case you want to look up the ID for the most recently imported file.

    'pixelfed.limit' => 10,
    'pixelfed.since_id' => 12345

## Tweaks

The posts are created in individual folders in a folder called 'temp' in this plugin. You can move them to you 'content' folder. I've added an extra level by year in my site, but you can ofcourse do something else entirely with your newly created pages.

## Example 

Check out the display on my site at
https://mirthe.org/fotofeed

Disclaimer: this started as an Instagram import, but I've since moved away from that platform.

## Blueprint

### Photopost.yml

    title: Photopost
    num: date
    icon: dashboard

    columns:
        left:
            width: 2/3
            fields:
            photo:
                type: files
                layout: cards

        right:
            width: 1/3
            fields:
            date:
                type: date
                time: true
            intro:
                label: Intro
                type: textarea
                size: small
            tags:
                type: tags
                options: query
                query: site.index.filterBy("template", "in", ["photopost"]).pluck("tags", ",", true)
            sourcelink:
                type: url

## Todo

- Offer as an official Kirby plugin
- Add sample Kirby templates to this readme
- Add more sample Kirby Blueprint to this readme
- Cleanup code
- Lots..
