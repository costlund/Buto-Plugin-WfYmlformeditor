# Buto-Plugin-WfYmlformeditor
Plugin to edit yml files.
Role webadmin is required.

## Settings
```
plugin_modules:
  ymlformeditor:
    plugin: wf/ymlformeditor
    settings:
      dir: '/theme/_theme_/_theme_/forms'
```
Param dir is folder where forms are.

## Url
```
http://localhost/ymlformeditor/forms
```

## Example
Example how to use.

### Form
A form to edit values in file home_alert.yml
```
name: 'Home alert'
file: /../buto_data/theme/[theme]/home_alert.yml
key: 
preview_skip: false
form:
  data:
    id: frm_test
    items:
      title:
        type: varchar
        label: Title
      description:
        type: text
        label: Description
        #html: true
      from:
        type: date
        label: From
      to:
        type: date
        label: To
      allow:
        type: varchar
        label: Allow
        option:
          '': 'No'
          '1': 'Yes'
```
Param preview_skip are used to preview data before go to edit mode.
Use key param if params not in yml root.
Param html must be true to use textarea as html editor.

### Data
Data in file home_alert.yml.
```
from: '2021-05-01'
to: '2021-05-31'
title: 'Title'
description: 'Description...'
allow: '1'
```

### Usage in page file
File home_alert are in this case used in a page file. Not relevant for this plugin usage.
```
content:
  -
    type: div
    settings:
      role:
        allow: true
        item:
          - client
      date:
        allow: yml:/../buto_data/theme/_theme_/_theme_/home_alert.yml:allow
        from: yml:/../buto_data/theme/_theme_/_theme_/home_alert.yml:from
        to: yml:/../buto_data/theme/_theme_/_theme_/home_alert.yml:to
    attribute:
      class: row
    innerHTML:
      -
        type: div
        attribute:
          class: col-md-12
        innerHTML:
          -
            type: div
            attribute:
              class: alert alert-warning
            innerHTML:
              -
                type: h1
                innerHTML: yml:/../buto_data/theme/_theme_/_theme_/home_alert.yml:title
              -
                type: p
                innerHTML: yml:/../buto_data/theme/_theme_/_theme_/home_alert.yml:description
              -
                type: p
                attribute:
                  class: text-center
                  style: 'font-size:smaller'
                innerHTML:
                  -
                    type: span
                    innerHTML: 'From'
                  -
                    type: span
                    innerHTML: yml:/../buto_data/theme/_theme_/_theme_/home_alert.yml:from
                  -
                    type: span
                    innerHTML: 'to'
                  -
                    type: span
                    innerHTML: yml:/../buto_data/theme/_theme_/_theme_/home_alert.yml:to
```
