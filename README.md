Speak
=====

Goals
-----
* Replace GlotPress: Think of this plugin as a GlotPress 2.0, in the form of a
  plugin.
* Create an easy to use interface: Speak is intended to be easy to use for
  everyone, without worrying about any of the underlying translation specifics.
* Provide a simple, thorough API: Speak's API is intended to be easy to use both
  internally, and externally (for things like the upcoming language packs)
* Reuse wherever possible: WordPress already includes the POMO library to handle
  a lot of translation tasks. GlotPress is an established project with a lot of
  history. Speak strives to reuse GlotPress' code where sensible, and rewrite
  the rest.
* Embeddable: Already have your project data in Wizbang type posts? Speak should
  be able to reuse your existing post types and simply attach translations to
  them, rather than reinventing the system.

Design
------
All data in Speak is implemented based on one of three post types:

* Strings: The basic unit of translation is the String. Any data that can be
  translated is represented as a String. Strings can have a parent String, which
  is called the "base" or "original" string.

* Projects: A Project is a collection of Strings, grouped together based on a
  common source. Projects can have parent Projects.

* Languages: A Language is a collection of Strings, grouped together based on
  the translation. Each Language (except one) has a parent Language, and all
  Strings associated with a Language are automatically copied (using
  copy-on-write) from the parent. A Language without a parent is the "default"
  Language, and does not inherit strings.

There also exist some common views:

* Translation Sets: A Translation Set is all Strings with a specific Language
  related to a specific Project. A Translation Set by its nature is a possibly
  sparse set, as not all Strings for a project may have been translated. If a
  String has not been translated, the Translation Set will show the parent
  Language's translation.

Contributing
------------
There's a lot of work to be done, and Speak is only in the design stage at the
moment. If you want to contribute, please contact me directly rather than
contributing patches.
