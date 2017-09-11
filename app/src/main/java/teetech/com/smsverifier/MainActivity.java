package teetech.com.smsverifier;

import android.content.ComponentName;
import android.content.ServiceConnection;
import android.os.IBinder;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;

public class MainActivity extends AppCompatActivity {

    MyService mService;
    boolean mBond = false;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);


    }

    private ServiceConnection mConnection = new ServiceConnection() {
        @Override
        public void onServiceConnected(ComponentName name, IBinder service)
        {
            MyService.MyBinder binder = (MyService.MyBinder)service;
            mService = (MyService)binder.getService();
            mBond = true;
        }

        @Override
        public void onServiceDisconnected(ComponentName name)
        {
            mService = null;
            mBond = false;
        }
    };
}
