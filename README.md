# Buto-Plugin-WfYmlformeditor

<ul>
<li>Plugin to edit yml files.</li>
<li>Role webadmin is required.</li>
</ul>

<a name="key_0"></a>

## Settings

<p>Param dir is folder where forms are.</p>
<pre><code>plugin_modules:
  ymlformeditor:
    plugin: wf/ymlformeditor
    settings:
      dir: '/theme/_theme_/_theme_/forms'</code></pre>
<p>Url. Could be open in a modal.</p>
<pre><code>http://localhost/ymlformeditor/forms</code></pre>

<a name="key_1"></a>

## Form

<p>A form to edit values in file home_alert.yml</p>
<pre><code>name: 'Home alert'
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
          '1': 'Yes'</code></pre>
<p>Param preview_skip are used to preview data before go to edit mode.
Use key param if params not in yml root.
Param html must be true to use textarea as html editor.</p>

<a name="key_2"></a>

## Data

<p>Data in file home_alert.yml.</p>
<pre><code>from: '2021-05-01'
to: '2021-05-31'
title: 'Title'
description: 'Description...'
allow: '1'</code></pre>

<a name="key_3"></a>

## Usage

<p>Usage in page file.
File home_alert are in this case used in a page file. Not relevant for this plugin usage.</p>
<pre><code>content:
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
                    innerHTML: yml:/../buto_data/theme/_theme_/_theme_/home_alert.yml:to</code></pre>

<a name="key_4"></a>

## Pages



<a name="key_4_0"></a>

### page_delete



<a name="key_4_1"></a>

### page_deletefile



<a name="key_4_2"></a>

### page_edit



<a name="key_4_3"></a>

### page_file



<a name="key_4_4"></a>

### page_forms



<a name="key_4_5"></a>

### page_home



<a name="key_4_6"></a>

### page_list



<a name="key_4_7"></a>

### page_update



<a name="key_4_8"></a>

### page_upload



<a name="key_4_9"></a>

### page_uploadsend



<a name="key_4_10"></a>

### page_view



<a name="key_5"></a>

## Widgets



<a name="key_6"></a>

## Event



<a name="key_7"></a>

## Construct



<a name="key_7_0"></a>

### __construct



<a name="key_8"></a>

## Methods



<a name="key_8_0"></a>

### init_data



<a name="key_8_1"></a>

### init_upload_form



<a name="key_8_2"></a>

### handleOutput



<a name="key_8_3"></a>

### delete_file



<a name="key_8_4"></a>

### setForm



<a name="key_8_5"></a>

### htmlentities



