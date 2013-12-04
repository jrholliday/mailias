<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <!-- Splash Page -->
  <xsl:output method="html" doctype-system="about:legacy-compat"/>
  <xsl:template match="Mailias">
    <html>
      <head>
	<meta charset='utf-8'/>
	<title>mAiLIAS</title>
	<link href="web/mailias.css" rel="stylesheet"/> 
     </head>
      <body>
	<header>mAiLIAS<img src="web/mailias.png" alt="logo"/></header>
        <article>
          <form>
            <div>
              <input type="text" name="q"/>
              <input type="submit" value="Check it!"/>
            </div>
          </form>
        </article>
	<footer>mAiLIAS - Personal ad hoc email addresses.</footer>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="web/mailias.js"></script>
      </body>
    </html>
  </xsl:template>
  <!-- All Mailboxes -->
  <xsl:output method="html" doctype-system="about:legacy-compat"/>
  <xsl:template match="Postoffice">
    <html>
      <head>
	<meta charset='utf-8'/>
	<title>mAiLIAS</title>
	<link href="web/mailias.css" rel="stylesheet"/> 
     </head>
      <body>
	<header>mAiLIAS<img src="web/mailias.png" alt="logo"/></header>
        <article>
	  <h1>Current Mailboxes</h1>
	  <table>
	    <tr>
	      <th>Mailbox</th>
	      <th>Count</th>
	      <th>Last Mail Received</th>
	    </tr>
	    <xsl:for-each select="Mailbox">
	      <tr>
	        <td><a><xsl:attribute name="href"><xsl:value-of select="User"/></xsl:attribute><xsl:value-of select="User"/></a></td>
	        <td><a><xsl:attribute name="href"><xsl:value-of select="User"/></xsl:attribute><xsl:value-of select="Count"/></a></td>
	        <td><a><xsl:attribute name="href"><xsl:value-of select="User"/></xsl:attribute><xsl:value-of select="Latest"/></a></td>
	      </tr>
	    </xsl:for-each>
	  </table>
        </article>
	<footer>mAiLIAS - Personal ad hoc email addresses.</footer>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="web/mailias.js"></script>
      </body>
    </html>
  </xsl:template>
  <!-- Single Mailbox -->
  <xsl:template match="Mailbox">
    <html>
      <head>
	<meta charset='utf-8'/>
	<title>[<xsl:value-of select="count(Email)"/>] <xsl:value-of select="User"/></title>
	<link href="web/mailias.css" rel="stylesheet"/> 
      </head>
      <body>
	<header>mAiLIAS<img src="web/mailias.png" alt="logo"/></header>
        <article>
          <h1>Mailbox for: <span><xsl:value-of select="User"/></span><a><xsl:attribute name="href"><xsl:value-of select="User"/>/rss</xsl:attribute><img src="web/rss.png" alt="RSS feed"/></a></h1>
	  <table>
	    <tr>
	      <th>From</th>
	      <th>Subject</th>
	      <th>Date</th>
	    </tr>
	    <xsl:for-each select="Email">
	      <tr>
	        <td><a><xsl:attribute name="href"><xsl:value-of select="../User"/>/<xsl:value-of select="Link"/></xsl:attribute><xsl:value-of select="From"/></a></td>
	        <td><a><xsl:attribute name="href"><xsl:value-of select="../User"/>/<xsl:value-of select="Link"/></xsl:attribute><xsl:value-of select="Subject"/></a></td>
	        <td><a><xsl:attribute name="href"><xsl:value-of select="../User"/>/<xsl:value-of select="Link"/></xsl:attribute><xsl:value-of select="Date"/></a></td>
	      </tr>
	    </xsl:for-each>
  	  </table>
        </article>
        <footer>mAiLIAS - Personal ad hoc email addresses.</footer>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="web/mailias.js"></script>
      </body>
    </html>
  </xsl:template>
  <!-- Message -->
  <xsl:template match="Message">
    <html>
      <head>
	<title><xsl:value-of select="Subject"/></title>
	<link href="../web/mailias.css" rel="stylesheet"/> 
      </head>
      <body>
	<header>mAiLIAS<img src="../web/mailias.png" alt="logo"/></header>
        <article>
          <div>
            <span>To:</span> <xsl:value-of select="To"/><br/>
  	    <span>From:</span> <xsl:value-of select="From"/><br/>
 	    <span>Subject:</span> <xsl:value-of select="Subject"/><br/>
	    <span>Date:</span> <xsl:value-of select="Date"/><br/>
            <button>Original</button>
            <button>Forward</button>
            <button>Delete</button>
	    <div><xsl:value-of select="Text"/></div>
  	    <pre><xsl:value-of select="Raw"/></pre>
          </div>
        </article>
        <footer>mAiLIAS - Personal ad hoc email addresses.</footer>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="../web/mailias.js"></script>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
