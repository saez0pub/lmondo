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
import shutil
import time
import threading
import hashlib    

def hash_get(file_name):
    # Open,close, read file and calculate MD5 on its contents 
    with open(file_name) as file_to_check:
      # read contents of the file
      data = file_to_check.read()    
      # pipe contents of the file through
      md5_returned = hashlib.md5(data).hexdigest()
    return md5_returned

def add_path(target):
    if target.find('/', 0, 0):
        return target
    else:
        return os.getcwd()+'/'+target

class LmondoListener(object):
    """GStreamer/PocketSphinx Demo Application"""
    def __init__(self):
        """Initialize a DemoApp object"""
        self.md5ConfOriginal = hash_get(os.getcwd()+'/../etc/lmondoListener.cfg')
        self.md5GrammOriginal = hash_get(os.getcwd()+'/../etc/grammar.jsgf')
        self.init_gst()
        self.timer = threading.Timer(10, self.check_config)
        self.timer.start()
    def read_config(self):
        updateFsg = os.popen('sphinx_jsgf2fsg -jsgf  '+os.getcwd()+'/../etc/grammar.jsgf -fsg  '+os.getcwd()+'/../etc/grammar.fsg 2>&1')
        print updateFsg.read()
        self.config = ConfigParser.ConfigParser()
        self.config.read(os.getcwd()+'/../etc/lmondoListener.cfg')
        self.recoName = self.config.get('config','reco_name')
        self.recoSpell = self.config.get('config','reco_spell')
        self.hmm = add_path(self.config.get('config','hmm'))
        self.dico = add_path(self.config.get('config','dict'))
        self.dictOrig = add_path(self.config.get('config','dict_orig'))
        self.fsg = add_path(self.config.get('config','fsg'))
        self.generate_dictionary()
    def generate_dictionary(self):
      trouve = False
      shutil.copyfile(self.dictOrig, self.dico)
      i = 0
      trouve = False
      for line in open(self.dico):
        if self.recoName in line:
          i += 1
          print line + " : " + self.recoSpell + " ?"
          if self.recoSpell in line:
            trouve = True
      if trouve is False:
        dico = open(self.dico, 'a')
        print "Ajout " + self.recoName+"("+str(i)+") "+self.recoSpell + " dans " + self.dico
        dico.write(self.recoName+"("+str(i)+") "+self.recoSpell)
        dico.close()
    def check_config(self):
        self.md5Conf = hash_get(os.getcwd()+'/../etc/lmondoListener.cfg')
        self.md5Gramm = hash_get(os.getcwd()+'/../etc/grammar.jsgf')
        print 'Test if '+self.md5Conf+' != '+self.md5ConfOriginal
        print ' or '+self.md5Gramm+' != '+self.md5GrammOriginal
        self.timer = threading.Timer(10, self.check_config)
        self.timer.start()
        if self.md5Conf != self.md5ConfOriginal or self.md5Gramm != self.md5GrammOriginal:
          print 'restart'
          self.md5Conf=self.md5ConfOriginal
          self.md5Gramm=self.md5GrammOriginal
          self.restart_listener()

    def init_gst(self):
        """Initialize the speech components"""
        self.read_config()
        self.pipeline = gst.parse_launch('gconfaudiosrc ! audioconvert ! audioresample '
                                         + '! vader name=vad auto-threshold=true '
                                         + '! pocketsphinx name=asr ! fakesink')
        self.asr = self.pipeline.get_by_name('asr')
        self.asr.connect('partial_result', self.asr_partial_result)
        self.asr.connect('result', self.asr_result)
        self.asr.set_property('hmm', self.hmm)
        self.asr.set_property('dict', self.dico)
        self.asr.set_property('fsg', self.fsg)
        self.asr.set_property('bestpath', 'no')
        self.asr.set_property('configured', True)
        self.bus = self.pipeline.get_bus()
        self.bus.add_signal_watch()
        self.bus_id =  self.bus.connect('message::application', self.application_message)
        self.pipeline.set_state(gst.STATE_PLAYING)
        self.started = True

    def asr_partial_result(self, asr, text, uttid):                                                                 
       """Forward partial result signals on the bus to the main thread."""                                         
       struct = gst.Structure('partial_result')                                                                    
       struct.set_value('hyp', text)                                                                               
       struct.set_value('uttid', uttid)                                                                            
       asr.post_message(gst.message_new_application(self.asr, struct))

    def asr_result(self, asr, text, uttid):
        """Forward result signals on the bus to the main thread."""
        struct = gst.Structure('result')
        struct.set_value('hyp', text)
        struct.set_value('uttid', uttid)
        asr.post_message(gst.message_new_application(self.asr, struct))

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
        os.popen('espeak -v mb/mb-fr4 -s 150 -p 40 " vous avez dit '+hyp+'"')
        self.pipeline.set_state(gst.STATE_PLAYING)
    def restart_listener(self):
        if self.started:
            self.pipeline.set_state(gst.STATE_NULL)
            self.pipeline.remove(self.asr)
            self.bus.disconnect(self.bus_id)
            self.started = False
        print "restart"
        self.init_gst()

app = LmondoListener()
gtk.main()
