package teetech.com.smsverifier;

import android.app.Service;
import android.content.Intent;
import android.os.Binder;
import android.os.IBinder;

public class MyService extends Service
{
    private IBinder myBinder = new MyBinder();

    public MyService()
    {

    }

    @Override
    public IBinder onBind(Intent intent)
    {
        return myBinder;
    }

    public class MyBinder extends Binder
    {
        MyService getService() {
            return MyService.this;
        }
    }

    @Override
    public int onStartCommand(Intent intent,int flag,int startId)
    {

        return START_STICKY;
    }
}
