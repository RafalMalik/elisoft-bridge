USE [klajmax]
GO
/****** Object:  Table [dbo].[ExtDokument]    Script Date: 2019-02-20 21:25:41 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ExtDokument](
	[ID] [int] NOT NULL,
	[Guid] [uniqueidentifier] NULL,
	[RodzajDokumentu] [int] NULL,
	[ID_Podmiotu] [int] NULL,
	[ID_Kontrahenta] [int] NULL,
	[ID_Kasy] [int] NULL,
	[MiejsceWystawienia] [nvarchar](100) NULL,
	[ZaplataDni] [int] NULL,
	[ZaplataSposob] [nvarchar](100) NULL,
	[NazwaBanku] [nvarchar](100) NULL,
	[NumerKonta] [nvarchar](1000) NULL,
	[DokumentWystawil] [nvarchar](100) NULL,
	[DokumentOdebral] [nvarchar](100) NULL,
	[WalutaSymbol] [nvarchar](50) NULL,
	[WalutaKurs] [decimal](19, 6) NULL,
	[LiczOdCenBrutto] [bit] NULL,
	[Uwagi] [nvarchar](1000) NULL,
	[WyslijMail] [bit] NULL,
	[Kontrahent_Nip] [nvarchar](25) NULL,
	[Kontrahent_Nazwa] [nvarchar](1000) NULL,
	[Kontrahent_Ulica] [nvarchar](50) NULL,
	[Kontrahent_Numer] [nvarchar](20) NULL,
	[Kontrahent_KodPocztowy] [nvarchar](50) NULL,
	[Kontrahent_Miejscowosc] [nvarchar](50) NULL,
	[Kontrahent_Panstwo] [nvarchar](50) NULL,
	[Kontrahent_Email] [nvarchar](100) NULL,
	[Kontrahent_Guid] [uniqueidentifier] NULL,
	[ProcessDate] [datetime] NULL,
	[ProcessStatus] [nvarchar](1000) NULL,
	[IsProcessed] [bit] NULL,
	[IsCompleted] [bit] NULL,
	[Extrernal_ID] [int] NULL,
	[Exterlnal_Symbol] [nvarchar](100) NULL,
 CONSTRAINT [PK_ExtDokument] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[ExtDokumentWiersz]    Script Date: 2019-02-20 21:25:41 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ExtDokumentWiersz](
	[ID] [int] NOT NULL,
	[GUID_Dokumentu] [uniqueidentifier] NULL,
	[ID_Magazynu] [int] NULL,
	[ID_Materialu] [int] NULL,
	[NazwaTowaru] [nvarchar](1000) NULL,
	[KodTowaru] [nvarchar](100) NULL,
	[KodPKWiU] [nvarchar](20) NULL,
	[Jednostka] [nvarchar](50) NULL,
	[Cena] [money] NOT NULL,
	[Ilosc] [money] NOT NULL,
	[Vat] [tinyint] NOT NULL,
	[Rabat] [money] NULL,
	[LiczOdCenBrutto] [bit] NULL,
	[Usuniety] [bit] NULL,
 CONSTRAINT [PK_ExtDokumentWiersz] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
