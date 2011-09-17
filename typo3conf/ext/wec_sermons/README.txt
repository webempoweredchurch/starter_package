Welcome to the "WEC Sermons" extension (often coined SMS for the "Sermon
Management System").  This extension provides TYPO3 users the ability to
post, organize and display a collection of sermons and associated content.
For complete information, please see the included manual (doc/manual.swx).

Caveats:
Inline Relational Record Editing (IRRE) has been utitilized for versions >=
0.9.5 (although I would avoid 0.9.5 like the plague).  This functionality can
significantly enhance the usability for entering Sermon records.  However, some
older Typo3 releases suffer from IRRE bugs where unusual behavior can result.
Specifically, if you find you're unable to reference preexisting records in
the IRRE select boxes, I suggest running >= v4.2beta1 (or install
the patch found at:

http://support.typo3.org/uploads/tx_nntpreader/0006007.patch
