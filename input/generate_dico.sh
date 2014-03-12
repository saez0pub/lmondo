#!/bin/bash

sphinx_jsgf2fsg -jsgf grammar.jsgf -fsg grammar.fsg
grep ^TRANSITION grammar.fsg | awk '{ print $5 }' |  grep -v '^$' | sort | uniq | while read word
do
  grep -e "^$word([0-9]" -e "^$word " -e "^-$word " /usr/local/share/pocketsphinx/model/lm/fr_FR/frenchWords62K.dic || echo KO $worg >&2
done 1> generated.dic
/usr/bin/pocketsphinx_continuous -dict generated.dic -hmm /usr/local/share/pocketsphinx/model/hmm/fr_FR/french_f0/   -lm /usr/local/share/pocketsphinx/model/lm/fr_FR/french3g62K.lm.dmp -jsgf grammar.jsgf
#/usr/bin/pocketsphinx_continuous -dict generated.dic -hmm /usr/local/share/pocketsphinx/model/hmm/fr_FR/french_f2/ -samprate 8000  -lm /usr/local/share/pocketsphinx/model/lm/fr_FR/french3g62K.lm.dmp -jsgf grammar.jsgf
