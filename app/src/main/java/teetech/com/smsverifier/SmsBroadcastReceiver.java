package teetech.com.smsverifier;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.provider.Telephony;
import android.telephony.SmsMessage;
import android.widget.Toast;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.ProtocolException;
import java.net.URL;
import java.net.URLEncoder;

import javax.net.ssl.HttpsURLConnection;

/**
 * Created by aKI on 24/08/2016.
 */

public class SmsBroadcastReceiver extends BroadcastReceiver
{
    String result;

    @Override
    public void onReceive(Context context, Intent intent)
    {

        if (Telephony.Sms.Intents.SMS_RECEIVED_ACTION.equals(intent.getAction()))
        {
            for (SmsMessage smsMessage : Telephony.Sms.Intents.getMessagesFromIntent(intent))
            {
                String messageBody = smsMessage.getMessageBody();
                String address = smsMessage.getOriginatingAddress();

                String[] s = messageBody.split(" ");

                Toast.makeText(context,s[1]+" "+s[2],Toast.LENGTH_LONG).show();


                try
                {
                    String urlParameters ="sender=" + URLEncoder.encode(address, "UTF-8") +"&keyword=" + URLEncoder.encode(s[1], "UTF-8")+"&value=" + URLEncoder.encode(s[2], "UTF-8");
                    URL url = new URL("http://");
                    HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
                    urlConnection.setReadTimeout(10000);
                    urlConnection.setConnectTimeout(15000);
                    urlConnection.setRequestMethod("POST");

                    urlConnection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");


                    urlConnection.setDoInput(true);


                    OutputStreamWriter osw = new OutputStreamWriter(urlConnection.getOutputStream());

                    osw.write(urlParameters);
                    osw.close();






                    int responseCode=urlConnection.getResponseCode();

                    if (responseCode == HttpsURLConnection.HTTP_OK)
                    {
                        String line;
                        BufferedReader br=new BufferedReader(new InputStreamReader(urlConnection.getInputStream()));
                        while ((line=br.readLine()) != null)
                        {
                            result+=line;
                        }

                        br.close();
                    }
                    else
                    {

                        result = responseCode+"----";
                    }

                }
                catch (MalformedURLException e)
                {
                    e.printStackTrace();
                } catch (ProtocolException e) {
                    e.printStackTrace();
                } catch (UnsupportedEncodingException e) {
                    e.printStackTrace();
                } catch (IOException e) {
                    e.printStackTrace();
                }


            }
        }

    }
}
