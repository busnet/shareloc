package com.example.shareloc;

import java.util.Calendar;

import android.app.AlarmManager;
import android.app.PendingIntent;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

public class MyScheduleReceiver extends BroadcastReceiver {
	  @Override
	  public void onReceive(Context context, Intent intent) {
		  Intent i = new Intent(context, LocationService.class);
		  context.startService(i);
	  }
}
