package com.example.shareloc;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.UUID;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;

import android.annotation.SuppressLint;
import android.content.Context;
import android.os.Build;
import android.provider.Settings.Secure;
import android.telephony.TelephonyManager;
import android.text.Html;

@SuppressLint("SimpleDateFormat")
public class Utils {
	final static String SHARELOC_URL = "http://shareloc.co/sloc/public";
	//final static String SHARELOC_URL = "http://shareloc.local/";
	
	public static void postDeviceReport(Context c, String latitude, String longitude, String accuracy) throws IOException{
		String code = getDeviceCode(c);
		
		HttpClient client = new DefaultHttpClient();
		HttpPost post = new HttpPost(SHARELOC_URL + "/device/report");
		
		List<NameValuePair> pairs = new ArrayList<NameValuePair>();
		
		pairs.add(new BasicNameValuePair("code", code));
		pairs.add(new BasicNameValuePair("lat", latitude));
		pairs.add(new BasicNameValuePair("long", longitude));
		pairs.add(new BasicNameValuePair("accuracy", accuracy));
		
		post.setEntity (new UrlEncodedFormEntity(pairs));

		HttpResponse response = client.execute(post);
	}
	
	public static String getDevicePhoneNo(Context c){
		TelephonyManager mTelephonyMgr;
	    mTelephonyMgr = (TelephonyManager) c.getSystemService(c.TELEPHONY_SERVICE); 
	    return mTelephonyMgr.getLine1Number();
	}
	
	public static String getDeviceCode(Context ctx){
		TelephonyManager tm = (TelephonyManager) ctx.getSystemService(Context.TELEPHONY_SERVICE);

	    String tmDevice = tm.getDeviceId();
	    String androidId = Secure.getString(ctx.getContentResolver(), Secure.ANDROID_ID);
	    String serial = null;
	    if(Build.VERSION.SDK_INT > Build.VERSION_CODES.FROYO) serial = Build.SERIAL;

	    if(tmDevice != null) return "01" + tmDevice;
	    if(androidId != null) return "02" + androidId;
	    if(serial != null) return "03" + serial;
	    // other alternatives (i.e. Wi-Fi MAC, Bluetooth MAC, etc.)

	    return null;
    }
}
