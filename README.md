# README #

This filter allows you to hide or show content based on the roles held by the current user in the current context.

### How do I get set up? ###

Place the files into filter/role and run notifications.

In order to use the filter use span tags within your content as below:

```html
<span class="hideforrole">Hello non students</span>
<span class="showforrole">Hello students</span>
```

Where "student" is the shortname for any given role. Note - the filter won't work if any other classes or IDs are set on this element.