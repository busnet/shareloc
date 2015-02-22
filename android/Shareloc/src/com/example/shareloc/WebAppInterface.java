package com.example.shareloc;

import java.io.IOException;

import android.content.Context;
import android.content.Intent;
import android.location.LocationManager;
import android.provider.Settings;
import android.webkit.JavascriptInterface;
import android.widget.Toast;

public class WebAppInterface {
    Context mContext;

    /** Instantiate the interface and set the context */
    WebAppInterface(Context c) {
        mContext = c;
    }

    /** Show a toast from the web page */
    @JavascriptInterface
    public void showToast(String toast) {
        Toast.makeText(mContext, toast, Toast.LENGTH_SHORT).show();
    }
    
    @JavascriptInterface
    public String getPhoneNo(){
    	return Utils.getDevicePhoneNo(mContext);
    }
    
    @JavascriptInterface
    public String getCode(){
    	return Utils.getDeviceCode(mContext);
    }
    
    @JavascriptInterface
    public void startReportService(){
    	Intent i = new Intent(mContext, LocationService.class);
    	mContext.startService(i);
    }
}