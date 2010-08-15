<h1>{$page->title}</h1>

{if !$cfg->doCheck || $cfg->error}

<p>We need some information about your News server (NNTP), please provide the following information:</p>
<form action="?" method="post">
	<table width="100%" border="0" style="margin-top:10px;" class="data highlight">
		<tr class="">
			<td>Server: </td>
			<td><input type="text" name="server" value="{$cfg->NNTP_SERVER}" /></td>
		</tr>
		<tr class="alt">
			<td>Username:</td>
			<td><input type="text" name="user" value="{$cfg->NNTP_USERNAME}" /></td>
		</tr>
		<tr class="">
			<td>Password:</td>
			<td><input type="text" name="pass" value="{$cfg->NNTP_PASSWORD}" /></td>
		</tr>
		<tr class="alt">
			<td>Port: </td>
			<td><input type="text" name="port" value="{$cfg->NNTP_PORT}" /></td>
		</tr>
		<tr class="">
			<td colspan="2">
			{if $cfg->error}
				The following error was encountered:<br />
				<span class="error">&bull; {$cfg->nntpCheck->message}</span><br /><br />
			{/if}
			<input type="submit" value="Test Connection" />
			</td>
		</tr>
	</table>
</form>

{/if}

{if $cfg->doCheck && !$cfg->error}
	<div align="center">
		<p>The news server setup is correct, you may continue to the next step.</p>
		<form action="step4.php"><input type="submit" value="Step four: Setup admin user" /></form> 
	</div>             
{/if}
