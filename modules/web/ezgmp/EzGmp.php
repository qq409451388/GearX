<?php
class EzGmp extends BaseEzHttp
{
    /**
     * 启动http服务
     */
    public function start(){
        Logger::console("[EzGmp]Start EzGmp Server...");
        try{
            $this->s();
        } catch (Exception $e){
            Logger::error("[EzGmp] Cause By {}, At {}:{}", $e->getMessage(), $e->getLine(). $e->getFile());
            $this->s();
        } catch (Throwable $t){
            Logger::error("[EzGmp] Cause By {}, At {}:{}", $t->getMessage(), $t->getLine(). $t->getFile());
            $this->s();
        }
    }

    private function s() {
        //创建socket套接字
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        // set the option to reuse the port
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        //为套接字绑定ip和端口
        @socket_bind($this->socket,$this->host,$this->port);
        //监听socket
        socket_listen($this->socket,4);
        //设置阻塞模式
        socket_set_block($this->socket);
        $isSucc = socket_last_error();
        if(0 == $isSucc){
            Logger::console("[EzGmp]Start Success ezgmp://".$this->host.":".$this->port);
        }else{
            $err = socket_strerror($isSucc);
            Logger::console("[EzGmp]Start Fail! ".$err);
            exit();
        }
        while(true)
        {
            //接收客户端请求
            if($msgsocket = socket_accept($this->socket)){
                //读取请求内容
                //$request = $this->buildRequest(socket_read($msgsocket, self::SOCKET_READ_LENGTH));
                //$response = $this->getResponse($request);
                $content = "";//$response->toString();
                socket_write($msgsocket, $content, strlen($content));
                socket_close($msgsocket);
            }
        }
    }

    protected function setTcpServerInstance()
    {
        // TODO: Implement setTcpServerInstance() method.
    }
}
