#!/usr/bin/env python

# Copyright (c) 2008 Carnegie Mellon University.
#
# You may modify and redistribute this file under the same terms as
# the CMU Sphinx system.  See
# http://cmusphinx.sourceforge.net/html/LICENSE for more information.

import pygtk
pygtk.require('2.0')
import gtk

import gobject
import pygst
pygst.require('0.10')
gobject.threads_init()
import gst
import os
import ConfigParser




def add_path(target):
    if target.find('/', 0, 0):
        return target
    else:
        return os.getcwd()+'/'+target

class LmondoListener(object):
    """GStreamer/PocketSphinx Demo Application"""
    def __init__(self):
        """Initialize a DemoApp object"""
        updateFsg = os.popen('sphinx_jsgf2fsg -jsgf  '+os.getcwd()+'/../etc/grammar.jsgf -fsg  '+os.getcwd()+'/../etc/grammar.fsg 2>&1')
        print updateFsg.read()
        self.read_config()
        self.init_gst()
    def read_config(self):
        self.config = ConfigParser.ConfigParser()
        self.config.read(os.getcwd()+'/../etc/lmondoListener.cfg')
    def init_gst(self):
        """Initialize the speech components"""
        self.pipeline = gst.parse_launch('gconfaudiosrc ! audioconvert ! audioresample '
                                         + '! vader name=vad auto-threshold=true '
                                         + '! pocketsphinx name=asr ! fakesink')
        asr = self.pipeline.get_by_name('asr')
        asr.connect('partial_result', self.asr_partial_result)
        asr.connect('result', self.asr_result)
        asr.set_property('hmm', '/usr/local/share/pocketsphinx/model/hmm/fr_FR/french_f2/')
        asr.set_property('dict', '/usr/local/share/pocketsphinx/model/lm/fr_FR/frenchWords62K.dic')
        asr.set_property('fsg', os.getcwd()+'/../etc/grammar.fsg')
        "asr.set_property('bestpath', 'yes')"
        asr.set_property('configured', True)

        bus = self.pipeline.get_bus()
        bus.add_signal_watch()
        bus.connect('message::application', self.application_message)
        self.pipeline.set_state(gst.STATE_PLAYING)

    def asr_partial_result(self, asr, text, uttid):                                                                 
       """Forward partial result signals on the bus to the main thread."""                                         
       struct = gst.Structure('partial_result')                                                                    
       struct.set_value('hyp', text)                                                                               
       struct.set_value('uttid', uttid)                                                                            
       asr.post_message(gst.message_new_application(asr, struct))

    def asr_result(self, asr, text, uttid):
        """Forward result signals on the bus to the main thread."""
        struct = gst.Structure('result')
        struct.set_value('hyp', text)
        struct.set_value('uttid', uttid)
        asr.post_message(gst.message_new_application(asr, struct))

    def application_message(self, bus, msg):
        """Receive application messages from the bus."""
        msgtype = msg.structure.get_name()
        if msgtype == 'partial_result':
            self.partial_result(msg.structure['hyp'], msg.structure['uttid'])
        elif msgtype == 'result':
            self.final_result(msg.structure['hyp'], msg.structure['uttid'])

    def partial_result(self, hyp, uttid):
        """Insert the final result."""
        # All this stuff appears as one single action
        print 'partial_result: ' + hyp

    def final_result(self, hyp, uttid):
        """Insert the final result."""
        # All this stuff appears as one single action)
        self.pipeline.set_state(gst.STATE_PAUSED)
        print 'final_result: ' + hyp
        "gtk.main_quit()"
        os.popen('espeak -v mb/mb-fr4 -s 150 -p 40 " vous avez dit '+hyp+'"')
        self.pipeline.set_state(gst.STATE_PLAYING)

app = LmondoListener()
gtk.main()
"while (True):
  gtk.main()
  print 'restart App'"
