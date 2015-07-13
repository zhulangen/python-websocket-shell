#-*- coding:utf-8 -*-
from websocket_server import WebsocketServer
import subprocess
import os
# Called for every client connecting (after handshake)
def new_client(client, server):
	print("New client connected and was given id %d" % client['id'])
	server.send_message_to_all("Hey all, a new client has joined us")


# Called for every client disconnecting
def client_left(client, server):
	print("Client(%d) disconnected" % client['id'])



cmd_dic={"1":'./tool/longtime.sh'}

def message_dispatch(client,message):
	try:
		pids= os.popen("ps -ef | grep %s |grep -v grep | awk '{print $2}'" % (cmd_dic[message]))
		pids=pids.read();
		if(pids==""):
			if(cmd_dic.has_key(message)):
				subp=subprocess.Popen(cmd_dic[message],shell=True,stdout=subprocess.PIPE)
				while subp.poll()==None:
				    server.send_message(client,subp.stdout.readline())
			else:
				server.send_message(client,"key don't exist!!!\n")
		else:
			server.send_message(client,"进程已经在运行了，请等待执行完成!!!\n")		
	except Exception as e:
			print("ERROR: message_dispatch: "+str(e))
# Called when a client sends a message
def message_received(client, server, message):
	if len(message) > 200:
		message = message[:200]+'..'
	message_dispatch(client,message)

PORT=8009
server = WebsocketServer(PORT)
server.set_fn_new_client(new_client)
server.set_fn_client_left(client_left)
server.set_fn_message_received(message_received)
server.run_forever()
