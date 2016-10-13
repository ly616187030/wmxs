
var  CODER_ALAW		= 0;
var  CODER_PCM		= 1;
var	 CODER_G729		= 3;
var	 CODER_SPEEX		= 20;
var	 CODER_ULAW		= 100;

var   UBOX_MODE_RECORD	= 0;		//录音模式， 通常使用的模式
var	  UBOX_MODE_DIAG		= 1;		//诊断模式， 用于捕获线路信息，供信号分析之用，支持的语音编码方式是CODER_PCM
var	  UBOX_MODE_CONFIG	= 2;		//配置模式，
var  hdl = -1;

var     UBOX_STATE_RESET     = 1;			//复位态，表示既非振铃也非摘机的状态。如果此前为振铃态，则此状态表示振铃已停止，如果此前为摘机态，则此状态表示已挂机。
var		UBOX_STATE_RINGING   = 2;			//振铃态，表示已检测到线路振铃信号，如果振铃停止，则将触发 UBOX_EVENT_RESET 事件，并汇报 UBOX_STATE_RESET 状态。
var		UBOX_STATE_HOOK_OFF  = 3;			//摘机态，
var		UBOX_STATE_HANG		 = 4;			  //悬空态，
var     UBOX_STATE_IDLE     = 5;
var		UBOX_STATE_REVERSE_HOOKOFF = 6;     //反向摘机，指软件摘机
var		UBOX_STATE_POSITIVIE_HOOKOFF = 7;   //正向摘机，指软件摘机

var  ubox={                    //全局声明
            lines:[],
			linenum:0
		 };


function AppendStatus(szStatus)
{
	/*document.getElementById("StatusArea").value +=szStatus;
	document.getElementById("StatusArea").value +="\r\n";*/
}

function Ubox_Plug_In(uboxhdl)
{
   
   var outdata = "设备插入 句柄号:"+uboxhdl;
   
   if(ubox.linenum == 2)      //不考虑三个通道
      return;
	  
   	ubox.linenum++;
   	if( ubox.lines[uboxhdl] == undefined ){
				ubox.lines[uboxhdl] = {};
			}

	ubox.lines[uboxhdl].handle = uboxhdl;
	ubox.lines[uboxhdl].callernumber = "";
    ubox.lines[uboxhdl].dtmfkyes = "";
	ubox.lines[uboxhdl].lineid = ubox.linenum;
	
	if(ubox.linenum == 1)
	{
	    hdl = ubox.lines[uboxhdl].handle;
     	/*document.getElementById("lineid1").disabled="false";
		document.getElementById("lineid1").value=uboxhdl;*/
	}
	else
	{
    	/*document.getElementById("lineid2").disabled="false";
		document.getElementById("lineid2").value=uboxhdl;*/
	}

   AppendStatus(outdata);

}

function Ubox_hookoff(uboxhdl)
{
  
   var outdata = "设备"+uboxhdl;

    AppendStatus(outdata+" 摘机");
	//Phonic_usb.RecordStream(uboxhdl, 0);

  // AppendStatus(outdata);
}

function Ubox_hookon(uboxhdl)
{
   var outdata = "设备"+uboxhdl;

    AppendStatus(outdata+" 挂机");
	//Phonic_usb.StopRecord(uboxhdl);
	//Phonic_usb.StopRecord(uboxhdl);

}

function Ubox_CallId(uboxHandle,callerNumber,callerTime,callerName)
{
  
   var outdata = "设备333"+uboxHandle+"号码："+callerNumber+"时间："+callerTime ;

    //AppendStatus(outdata);
    var url = 'http://'+document.domain+'/index.php?s=/receiverorder/fastfood/getPerson';
    $.get(url,'phone='+callerNumber).success(function(data){
       if(data != null){
           var data = data;
           var gender = data[0]['gender'] == 1?'男':'女';
           var dt = '<dt><p>姓名：<span class="username">'+data[0]['username']+'</span></p><p>性别：<span class="gender">'+gender+'</span></p><p>电话：<span class="telephone">'+data[0]['phone']+'</span></p></dt>';
           var dd = '';
           for(var i= 0,ln = data.length;i<ln;i++){
               dd+= '<dd><p>地址('+(i+1)+')：'+data[i]['detail_address']+'</p><button title="'+data[i]['detail_address']+'" class="btn btn-primary">使用该地址</button></dd>';
           }
           dt+=dd;
       }else{
           dt = '<dt><p>电话：<span class="telephone">'+callerNumber+'</span></p><p>暂没有客户信息！</p><button class="btn btn-primary">使用该电话</button></dt>';
       }
        $('.tel-pop').show().find('dl').empty().append(dt);
    });
}


function ubox_Ring(uboxhdl)
{
   var outdata = "设备"+uboxhdl;

    AppendStatus(outdata+" 振铃");

}

function ubox_RingCancel(uboxhdl)
{
   var outdata = "设备"+uboxhdl;

    AppendStatus(outdata+" 振铃取消");

}

function ubox_RingStop(uboxhdl)
{
   var outdata = "设备"+uboxhdl;

    AppendStatus(outdata+" 停振");

}

function ubox_ToneBusy(uboxhdl)
{
   var outdata = "设备"+uboxhdl;

    AppendStatus(outdata+" 忙音");

}

function ubox_DeviceAlarm(uboxhdl)
{
   var outdata = "设备"+uboxhdl;

    AppendStatus(outdata+" 警告，重启软件");

}

function ubox_DeviceError(uboxhdl)
{
   var outdata = "设备"+uboxhdl;

    AppendStatus(outdata+" 错误，重启软件");

}

function ubox_DtmfKey(uboxHandle,dtmfCode)
{

   var outdata = "设备"+uboxHandle+"按键:"+(dtmfCode-48);

    AppendStatus(outdata);

}

function ubox_HangIng(uboxhdl)
{
   var outdata = "设备"+uboxhdl;

    AppendStatus(outdata+" 悬空");

}

function ubox_LineVoltage(uboxhdl,voltage)
{
   var outdata = "设备"+uboxhdl+"线路电压:"+voltage;

    AppendStatus(outdata);

}

function ubox_PlayEnd(uboxhdl)
{
   var outdata = "设备"+uboxhdl;

    AppendStatus(outdata+" 放音结束");

}

function ubox_PlugOut(uboxhdl)
{
   var outdata = "设备"+uboxhdl;
   
  /* for(int i=1;i<=ubox.linenum;i++)
   {
       if(ubox.lines[i]==undefine)
   }*/
  ubox.lines[uboxhdl].handle = -1;
  ubox.lines[uboxhdl] = undefined;
  ubox.linenum -= 1;
  AppendStatus(outdata+" 拨出");
  //hdl=-1;

}

function ubox_PlayError(uboxhdl)
{
   var outdata = "设备"+uboxhdl;

    AppendStatus(outdata+" 放音错误");

}

function ubox_openDevice(id)
{

 try{
 	
 	var uChannelNum=Phonic_usb.OpenDevice(UBOX_MODE_RECORD);
 	if(uChannelNum== 0)
 	{
 		AppendStatus("打开设备成功");

 	}
	else
 	{
	 	AppendStatus("打开设备失败");		
 	}
 }catch(e)
 {
 }

}

function ubox_CloseDevice()
{

	Phonic_usb.CloseDevice();
	AppendStatus("关闭设备完成.");

}


function ubox_RecordFile()
{

	var rec_name = "d:\\rec.wav";
	if(Phonic_usb.RecordFile(hdl,rec_name,CODER_ALAW) !=0)
	{
       AppendStatus("录音失败")
	}
	
}

function ubox_StopRecord()
{

	if(Phonic_usb.StopRecord(hdl) !=0)
	{
       AppendStatus("停止录音失败")
	}
}

function ubox_deviceClose()
{
	Phonic_usb.CloseDevice();
}



function ubox_playfile(szFile)
{
	uPlayFileID=Phonic_usb.PlayFile(hdl,szFile);
	if(uPlayFileID < 0)
	{
	 	AppendStatus("播放失败:"+szFile);	
	}else
	{
		AppendStatus("开始播放文件:"+szFile);
	}
}

function ubox_stopplay()
{
	Phonic_usb.StopPlay(hdl);
	AppendStatus("停止播放");
}

function ubox_dialnum(szCode)
{
    var state = Phonic_usb.GetLineState(hdl);
	if( (state== UBOX_STATE_HOOK_OFF )||(state== UBOX_STATE_REVERSE_HOOKOFF )||(state== UBOX_STATE_POSITIVIE_HOOKOFF ))
    {   

	  if(Phonic_usb.SendDtmf(hdl,szCode) == 0)
	  {
		AppendStatus("拨号:"+szCode);
	  }else
	  {
		AppendStatus("拨号失败:"+szCode);
	  }
	}
	else
	   AppendStatus("请摘起电话机")
}


function ubox_Start_read_line_voltage()
{
  if(Phonic_usb.StartReadLineVoltage(hdl) == 0)
     AppendStatus("开始量线路电压成功");
  else
     AppendStatus("开始量线路电压失败");
}

function ubox_Stop_read_line_voltage()
{
  if(Phonic_usb.StopReadLineVoltage(hdl) == 0)
     AppendStatus("停止量线路电压成功");
  else
     AppendStatus("停止量线路电压失败");
}

function ubox_Get_DeviceVersion()
{
  var ver = Phonic_usb.GetDeviceVersionNum(hdl);
     AppendStatus("硬件版本号"+ver);

}

function ubox_Get_User()
{
  var use_num = Phonic_usb.GetUsernum(hdl);
     AppendStatus("用户号:"+use_num);
}

function ubox_read_eeprom()
{
   var read_data = Phonic_usb.ReadEepromex2(hdl,0,8);
     AppendStatus("读eepromEx:"+read_data);

}


function ubox_write_eeprom()
{
    var write_data = "A23456B9";
	if(Phonic_usb.WriteEepromex(hdl,0,write_data,8)==0)
	{
		AppendStatus("写eeprom成功");
	}
	else
	{
      AppendStatus("写eeprom失败");
	}
}

function ubox_hook_off()
{

         //fi3102A fi3101A 支持

	  if(Phonic_usb.SoftHookoff(hdl)==0)
       {
		AppendStatus("摘机成功");
       }
	   else
	   {
         AppendStatus("摘机失败");
	   }

}

function ubox_hook_on()
{
        //fi3102A fi3101A 支持
 	if(Phonic_usb.SoftHookon(hdl)==0)
       {
		AppendStatus("挂机成功");
       }
	   else
	   {
         AppendStatus("挂机失败");
	   }
	
}

function ubox_handle_streamVoice(uboxHandle, pVoice, size)
{
	 var outdata = "流式录音 句柄号:"+hdl +"size:"+size;
   AppendStatus(outdata);
}

function changelinenum(This)
{
	if(ubox.lines[This.value] == undefined)
	   alert("USB盒子未插入");
	 else
	    hdl = ubox.lines[This.value].handle; 
   
}

 function   OCXRegistered(strID)
{  
  var   ocx=null;  
  try{  
      ocx=new   ActiveXObject(strID);  
     }catch(e){  
     return   false;  
    }  
  if(ocx!=null)
  {  
    ocx=null;  
    return   true;  
  }
  else  
     return   false;  
  }  